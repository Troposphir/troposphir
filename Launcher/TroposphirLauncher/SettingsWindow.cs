using System;
using System.Collections.Generic;
using System.Reflection;
using System.Linq;
using System.IO;

namespace TroposphirLauncher {
	public partial class SettingsWindow : Gtk.Window {
		public static readonly FileInfo CONFIG_PATH = new FileInfo(System.IO.Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.ApplicationData), "Troposphir", "Launcher", "settings.txt"));

		[SerializableSetting]
		public string AtmosphirExecutableFolder {
			get { return atmoPathTextBox.Text; }
			set { atmoPathTextBox.Text = value; }
		}
		[SerializableSetting]
		public string TroposphirServerPath {
			get { return serverUrlTextBox.Text; }
			set { serverUrlTextBox.Text = value; }
		}
		[SerializableSetting]
		public bool OnlineMode {
			get { return onlineCheckbox.Active; }
			set { onlineCheckbox.Active = value; }
		}
		[SerializableSetting]
		public bool AutoUpdate {
			get { return autoUpdateCheckbox.Active; }
			set { autoUpdateCheckbox.Active = value; }
		}

		public SettingsWindow () : base (Gtk.WindowType.Toplevel) {
			Build();
			LoadSettings();
			HideAll();
		}

		protected void OpenAtmoPathSelector (object sender, EventArgs e) {
			Gtk.FileChooserDialog dialog = new Gtk.FileChooserDialog("Select the Atmosphir installation folder", this, Gtk.FileChooserAction.SelectFolder);
			dialog.AddButton("Cancel", -1);
			dialog.AddButton("Select Folder", 1);
			if (dialog.Run() == 1) {
				AtmosphirExecutableFolder = new Uri(dialog.Uri).GetComponents(UriComponents.Path, UriFormat.Unescaped);
			}
			dialog.Destroy();
		}

		public void LoadSettings () {
			Dictionary<string, string> settings = new Dictionary<string,string>();
			if (!CONFIG_PATH.Exists) {
				CONFIG_PATH.Create().Close();
			}
			string[] configLines = File.ReadAllLines(CONFIG_PATH.ToString());
			foreach (string line in configLines) {
				string[] parts = line.Split("===".ToCharArray(), StringSplitOptions.RemoveEmptyEntries);
				if (parts.Length >= 2) {
					settings.Add(parts[0], parts[1]);
				}
			}

			IEnumerable<PropertyInfo> propertiesToLoad = GetSerializableProperties();
			propertiesToLoad.All(property => {
				if (settings.ContainsKey(property.Name)) {
					if (property.PropertyType == typeof(string)) {
						property.SetValue(this, settings[property.Name]);
					} else if (property.PropertyType == typeof(bool)) {
						property.SetValue(this, settings[property.Name].ToLower() == "true");
					} else if (property.PropertyType == typeof(int)) {
						property.SetValue(this, int.Parse(settings[property.Name]));
					} else if (property.PropertyType == typeof(float)) {
						property.SetValue(this, float.Parse(settings[property.Name]));
					} else {
						System.Diagnostics.Debug.Write(string.Format("Setting {0} isn't of a supported type.", property.Name));
					}
				}
				return true;
			});
		}

		public void SaveSettings (object sender, EventArgs e) {
			List<String> lines = new List<String> ();
			GetSerializableProperties().All(property => {
				lines.Add(property.Name+"==="+property.GetValue(this));
				return true;
			});
			File.WriteAllLines(CONFIG_PATH.ToString(), lines);
		}

		private IEnumerable<PropertyInfo> GetSerializableProperties() {
			return this.GetType().GetProperties().Where((prop, index) => {
				return prop.CustomAttributes.Any(data => {
					return data.AttributeType == typeof(SerializableSetting);
				});
			});
		}

		protected void OnDelete (object o, Gtk.DeleteEventArgs args) {
			Hide();
			args.RetVal = true;
		}
	}

	[AttributeUsage(AttributeTargets.Property)]
	public class SerializableSetting : System.Attribute {}
}

