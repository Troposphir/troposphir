using System.Security.Cryptography;
using System.Collections.Generic;
using System.Diagnostics;
using System.Threading;
using System.Linq;
using System.Net;
using System.IO;
using System;
using Gtk;

namespace TroposphirLauncher {
	public partial class MainWindow: Gtk.Window {
		private SettingsWindow settingsWindow;
		public MainWindow() : base (Gtk.WindowType.Toplevel) {
			Build();
			settingsWindow = new SettingsWindow();
			LoadNews();
			if (settingsWindow.AutoUpdate) {
				Thread updater = new Thread(new ThreadStart(CheckUpdates));
				updater.Start();
			}
		}

		protected void OnDeleteEvent (object sender, DeleteEventArgs a) {
			Application.Quit();
			a.RetVal = true;
		}

		protected void OpenSettingsWindow (object sender, EventArgs e) {
			settingsWindow.LoadSettings();
			settingsWindow.ShowAll();
		}

		protected void LaunchGame (object sender, EventArgs e) {
			File.WriteAllText(System.IO.Path.Combine (settingsWindow.AtmosphirExecutableFolder, "request.txt"), settingsWindow.TroposphirServerPath+"\n\r"+settingsWindow.TroposphirServerPath);
			Process atmosphirProcess = new Process();
			atmosphirProcess.StartInfo.FileName = System.IO.Path.Combine(settingsWindow.AtmosphirExecutableFolder, "Atmosphir.exe");
			object[] args = new object[] {settingsWindow.OnlineMode?"standalone":"offline", Screen.Width, Screen.Height, 60}; //Gtk# doesn't expose refresh rate, assume standard
			atmosphirProcess.StartInfo.Arguments = string.Format ("{0} {1}x{2}@{3}", args);
			atmosphirProcess.StartInfo.WorkingDirectory = settingsWindow.AtmosphirExecutableFolder;
			try {
				atmosphirProcess.Start();
			} catch {
				Debug.WriteLine("Atmosphir path not set");
				new AlertWindow("Atmosphir path not set!\nPlease select it in the\nsettings panel");
			}
		}

		string ServerRequest(string path) {
			string url = System.IO.Path.Combine(settingsWindow.TroposphirServerPath, path);
			WebRequest request = WebRequest.Create(url);
			Stream data = request.GetResponse().GetResponseStream();
			data.ReadTimeout = 3000;
			using (StreamReader reader = new StreamReader(data)) {
				return reader.ReadToEnd();
			}
		}

		void LoadNews() {
			string result = string.Empty;
			try {
				result = ServerRequest("updateNews.php");
			} catch (Exception e) {
				Debug.WriteLine("Could not connect to update news: "+e.Message);
				result = "Could not load update list";
			}
			updateTextView.Buffer.Clear();
			updateTextView.Buffer.InsertAtCursor(result);
		}

		void CheckUpdates() {
			Dictionary<string, string> fileHashes = new Dictionary<string, string>();
			using (MD5 md5 = MD5.Create()) {
				progressBar.Text = "Verifying game files";
				List<string> files = Glob.ListAllFiles(settingsWindow.AtmosphirExecutableFolder);
				for (int index = 0, length = files.Count; index < length; index++) {
					string entry = System.IO.Path.Combine(settingsWindow.AtmosphirExecutableFolder, files[index]);
					using (FileStream stream = File.OpenRead(entry)) {
						fileHashes.Add(entry.Replace("\\", "/"), BitConverter.ToString(md5.ComputeHash(stream)));
						progressBar.Fraction = index / (float)(length-1);
					}
				}
				//Computer-Specific dump of the game hashes for internal server testing
//				string ss = "";
//				foreach (string key in fileHashes.Keys) {
//					string uri = Uri.UnescapeDataString(new Uri(settingsWindow.AtmosphirExecutableFolder).MakeRelativeUri(new Uri(key)).ToString());
//					ss += uri + "===" + fileHashes[key] + "\r\n";
//				}
//				File.WriteAllText("C:/xampp/htdocs/Troposphir/Server/clientHashList.txt", ss);
			}
			string result = string.Empty;
			progressBar.Text = "Loading update list";
			progressBar.Fraction = 0f;
			try {
				result = ServerRequest("clientHashList.txt");
				List<UpdateItem> mustUpdate = new List<UpdateItem>();
				string[] lines = result.Split("\n".ToCharArray(), StringSplitOptions.RemoveEmptyEntries);
				for (int index = 0, length = lines.Length; index < length; index++) {
					string[] parts = lines[index].Split("===".ToCharArray(), StringSplitOptions.RemoveEmptyEntries);
					if (parts.Length == 2) {
						string key = System.IO.Path.Combine(new DirectoryInfo(settingsWindow.AtmosphirExecutableFolder).Parent.FullName, parts[0])
							.Replace("\\", "/");
						if (fileHashes[key] != parts[1]) {
							Debug.WriteLine(key);
							mustUpdate.Add(new UpdateItem(key, parts[0]));
						}
					}
					progressBar.Fraction = index / (float)(length-1);
				}
			} catch (Exception e) {
				Debug.WriteLine("Could not connect to update server: "+e.Message);
				progressBar.Text = "Could not connect";
			}
		}
	}
}
