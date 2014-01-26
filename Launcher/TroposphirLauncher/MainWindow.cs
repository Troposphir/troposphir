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
		public static readonly string TITLE_FORMAT = "Troposphir Launcher %";
		private ServerConnector server;
		private Updater updater;
		public string statusString;

		public string StatusString {
			get {
				return statusString;
			}
			set {
				progressBar.Text = value;
				statusString = value;
			}
		}

		public MainWindow() : base (Gtk.WindowType.Toplevel) {
			Build();
			FormatTitle();
			server = new ServerConnector(TroposphirLauncher.Settings.TroposphirServerPath);
			updater = new Updater(new Updater.Config() {
				Server = server,
				PatchFolder = TroposphirLauncher.Settings.AtmosphirExecutableFolder,
				Progress = (fraction) => {
					progressBar.Fraction = fraction;
					FormatTitle();
				},
				StepChange = (status) => {
					switch (status) {
						case Updater.UpdateStatus.NOT_STARTED:
							StatusString = "";
							break;
						case Updater.UpdateStatus.STARTING:
							StatusString = "Connecting to update server";
							break;
						case Updater.UpdateStatus.CHECKING:
							StatusString = "Verifying local files";
							break;
						case Updater.UpdateStatus.COMPARING:
							StatusString = "Looking for updates";
							break;
						case Updater.UpdateStatus.DOWNLOADING:
							StatusString = "Downloading patches";
							break;
						case Updater.UpdateStatus.APPLYING:
							StatusString = "Applying changes";
							break;
						case Updater.UpdateStatus.DONE:
							StatusString = "Finished updating";
							break;
						case Updater.UpdateStatus.FAILED:
							StatusString = "Failed updating the game";
							break;
						default:
							break;
					}
				}
			});
			LoadNews();
			if (TroposphirLauncher.Settings.AutoUpdate) {
				updater.AsyncUpdate();
			}
		}

		protected void OnDeleteEvent(object sender, DeleteEventArgs a) {
			Application.Quit();
			a.RetVal = true;
		}

		protected void OpenSettingsWindow(object sender, EventArgs e) {
			new SettingsWindow().Show();
		}

		protected void LaunchGame(object sender, EventArgs e) {
			File.WriteAllText(	System.IO.Path.Combine (TroposphirLauncher.Settings.AtmosphirExecutableFolder, "request.txt"), 
								TroposphirLauncher.Settings.TroposphirServerPath+"\n\r"+TroposphirLauncher.Settings.TroposphirServerPath);
			Process atmosphirProcess = new Process();
			atmosphirProcess.StartInfo.FileName = System.IO.Path.Combine(TroposphirLauncher.Settings.AtmosphirExecutableFolder, "Atmosphir.exe");
			object[] args = new object[] {
				TroposphirLauncher.Settings.OnlineMode? "standalone":"offline", 
				Screen.Width, 
				Screen.Height, 
				60 //Gtk# doesn't expose refresh rate, assume standard
			};
			atmosphirProcess.StartInfo.Arguments = string.Format ("{0} {1}x{2}@{3}", args);
			atmosphirProcess.StartInfo.WorkingDirectory = TroposphirLauncher.Settings.AtmosphirExecutableFolder;
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
				result = server.Request("updateNews.php");
			} catch (Exception e) {
				Debug.WriteLine("Could not connect to update news: "+e.Message);
				result = "Could not load update list";
			}
			updateTextView.Buffer.Clear();
			updateTextView.Buffer.InsertAtCursor(result);
		}

		void CheckUpdates() {
			updater.AsyncUpdate();
		}

		void FormatTitle() {
			int percent = (int)Math.Round(progressBar.Fraction * 100);
			if (percent > 0) {
				Title = TITLE_FORMAT.Replace("%", percent + "%");
			} else {
				Title = TITLE_FORMAT.Replace("%", "");
			}
		}
	}
}
