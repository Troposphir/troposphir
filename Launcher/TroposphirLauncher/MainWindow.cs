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
			Process atmosphirProcess = new Process();
			atmosphirProcess.StartInfo.FileName = System.IO.Path.Combine(settingsWindow.AtmosphirExecutableFolder, "Atmosphir.exe");
			object[] args = new object[] {settingsWindow.OnlineMode?"standalone":"offline", Screen.Width, Screen.Height, 60}; //Gtk# doesn't expose refresh rate, assume standard
			atmosphirProcess.StartInfo.Arguments = string.Format ("{0} {1}x{2}@{3}", args);
			atmosphirProcess.StartInfo.WorkingDirectory = settingsWindow.AtmosphirExecutableFolder;
			atmosphirProcess.Start();
		}

		async void LoadNews() {
			Task task = new Task(async () => {
				updateTextView.Buffer.Clear();
				updateTextView.Buffer.InsertAtCursor(await LoadNewsAsync());
			});
			task.Start();
			await task;
		}
		async Task<String> LoadNewsAsync() {
			string path = System.IO.Path.Combine(settingsWindow.TroposphirServerPath, "updateNews.php");
			WebRequest request = WebRequest.Create(path);
			Stream data = request.GetResponse().GetResponseStream();
			using (StreamReader reader = new StreamReader(data)) {
				return await reader.ReadToEndAsync();
			}
		}
	}
}
