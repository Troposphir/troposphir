using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Reflection;
using System.Linq;
using System.IO;

namespace TroposphirLauncher {
	public partial class SettingsWindow : Gtk.Window {

		public SettingsWindow() : base(Gtk.WindowType.Toplevel) {
			Build();
			atmoPathTextBox.Text = TroposphirLauncher.Settings.AtmosphirExecutableFolder??"";
			serverUrlTextBox.Text = TroposphirLauncher.Settings.TroposphirServerPath??"";
			autoUpdateCheckbox.Active = TroposphirLauncher.Settings.AutoUpdate;
			onlineCheckbox.Active = TroposphirLauncher.Settings.OnlineMode;
			TroposphirLauncher.Settings.OnAtmosphirExecutableFolderChanged += (newValue) => atmoPathTextBox.Text = (string)newValue;
			TroposphirLauncher.Settings.OnTroposphirServerPathChanged += (newValue) => serverUrlTextBox.Text = (string)newValue;
			TroposphirLauncher.Settings.OnAutoUpdateChanged += (newValue) => autoUpdateCheckbox.Active = (bool)newValue;
			TroposphirLauncher.Settings.OnOnlineModeChanged += (newValue) => onlineCheckbox.Active = (bool)newValue;
		}

		protected void OpenAtmoPathSelector(object sender, EventArgs e) {
			Gtk.FileChooserDialog dialog = new Gtk.FileChooserDialog("Select the Atmosphir installation folder", this, Gtk.FileChooserAction.SelectFolder);
			dialog.AddButton("Cancel", -1);
			dialog.AddButton("Select Folder", 1);
			if (dialog.Run() == 1) {
				TroposphirLauncher.Settings.AtmosphirExecutableFolder = new Uri(dialog.Uri).GetComponents(UriComponents.Path, UriFormat.Unescaped);
			}
			dialog.Destroy();
		}

		protected void OnDelete(object o, Gtk.DeleteEventArgs args) {
			Hide();
			args.RetVal = true;
		}

		protected void UpdateExecutablePath(object sender, EventArgs e) {
			TroposphirLauncher.Settings.AtmosphirExecutableFolder = atmoPathTextBox.Text;
		}

		protected void UpdateServerAddress(object sender, EventArgs e) {
			TroposphirLauncher.Settings.TroposphirServerPath = serverUrlTextBox.Text;
		}

		protected void UpdateOnlineMode(object sender, EventArgs e) {
			TroposphirLauncher.Settings.OnlineMode = onlineCheckbox.Active;
		}

		protected void UpdateAutoUpdate(object sender, EventArgs e) {
			TroposphirLauncher.Settings.AutoUpdate = autoUpdateCheckbox.Active;
		}

		protected void SaveSettings(object sender, EventArgs e) {
			TroposphirLauncher.Settings.Save();
		}
	}
}

