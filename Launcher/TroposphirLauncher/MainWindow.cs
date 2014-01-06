using System.Threading.Tasks;
using System.Diagnostics;
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
			progressBar.Fraction = 0.1;
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

		void LoadNews() {
			string result = string.Empty;
			try {
				string path = System.IO.Path.Combine(settingsWindow.TroposphirServerPath, "updateNews.php");
				WebRequest request = WebRequest.Create(path);
				Stream data = request.GetResponse().GetResponseStream();
				data.ReadTimeout = 3000;
				using (StreamReader reader = new StreamReader(data)) {
					result = reader.ReadToEnd();
				}
			} catch {
				Debug.WriteLine("Could not connect to update news");
				result = "Could not load update list";
			}
			updateTextView.Buffer.Clear();
			updateTextView.Buffer.InsertAtCursor(result);
		}
	}
}
