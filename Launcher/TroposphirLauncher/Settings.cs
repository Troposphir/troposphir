using System;
using System.IO;
using System.Collections.Generic;
using System.Reflection;
using System.Diagnostics;
using System.Linq;

namespace TroposphirLauncher {
	public static class Settings {
		public delegate void OnSettingChanged(object newValue);

		/// <summary>
		/// The base data path for all of the launcher's files.
		/// </summary>
		public static readonly DirectoryInfo DATA_PATH = new DirectoryInfo(Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.ApplicationData), "Troposphir", "Launcher"));
		/// <summary>
		/// Where to store the local file hashes.
		/// </summary>
		public static readonly FileInfo HASHDUMP_PATH = new FileInfo(Path.Combine(DATA_PATH.FullName, "hashDump.txt"));
		/// <summary>
		/// Where to save and load the launcher's configurations.
		/// </summary>
		public static readonly FileInfo CONFIG_PATH = new FileInfo(Path.Combine(DATA_PATH.FullName, "settings.txt"));

		public static readonly DirectoryInfo TEMP_PATH = new DirectoryInfo(Path.Combine(DATA_PATH.FullName, "Temp"));
		
		[SerializableSetting("")]
		static string atmosphirExecutableFolder;
		/// <summary>
		/// Gets or sets the Atmosphir executable folder.
		/// </summary>
		/// <value>The Atmosphir executable folder.</value>
		public static string AtmosphirExecutableFolder {
			get { return atmosphirExecutableFolder; }
			set {
				if (OnAtmosphirExecutableFolderChanged != null) OnAtmosphirExecutableFolderChanged(value);
				atmosphirExecutableFolder = value;
			}
		}
		public static event OnSettingChanged OnAtmosphirExecutableFolderChanged;

		[SerializableSetting("http://onemoreblock.com/Atmosphir/")]
		static string troposphirServerPath;
		/// <summary>
		/// Gets or sets the Troposphir server path.
		/// </summary>
		/// <value>The Troposphir server path.</value>
		public static string TroposphirServerPath {
			get { return troposphirServerPath; }
			set {
				if (OnTroposphirServerPathChanged != null) OnTroposphirServerPathChanged(value);
				troposphirServerPath = value;
			}
		}
		public static event OnSettingChanged OnTroposphirServerPathChanged;

		[SerializableSetting(true)]
		static bool onlineMode;
		/// <summary>
		/// Gets or sets a value indicating whether the game will be launched in online mode.
		/// </summary>
		/// <value><c>true</c> if online mode; otherwise, <c>false</c>.</value>
		public static bool OnlineMode {
			get { return onlineMode; }
			set {
				if (OnOnlineModeChanged !=null) OnOnlineModeChanged(value);
				onlineMode = value;
			}
		}
		public static event OnSettingChanged OnOnlineModeChanged;

		[SerializableSetting(false)]
		static bool autoUpdate;
		/// <summary>
		/// Gets or sets a value indicating whether this launcher will update the game when it starts.
		/// </summary>
		/// <value><c>true</c> if auto update; otherwise, <c>false</c>.</value>
		public static bool AutoUpdate {
			get { return autoUpdate; }
			set {
				if (OnAutoUpdateChanged != null) OnAutoUpdateChanged(value);
				autoUpdate = value;
			}
		}
		public static event OnSettingChanged OnAutoUpdateChanged;

		/// <summary>
		/// Loads the settings from the defined constant value CONFIG_PATH.
		/// </summary>
		public static void Load() {
			Dictionary<string, string> settings = new Dictionary<string,string>();
			if (!CONFIG_PATH.Exists) CONFIG_PATH.Create().Close();
			if (!TEMP_PATH.Exists) TEMP_PATH.Create();
			string[] configLines = File.ReadAllLines(CONFIG_PATH.ToString());
			ReadConfigLines(configLines, (k, v) => settings.Add(k, v));

			IEnumerable<FieldInfo> fieldsToLoad = SerializableSettingAttribute.GetSerializableFields(typeof(Settings));
			fieldsToLoad.All(field => {
				if (field.FieldType == typeof(string)) {
					if (settings.ContainsKey(field.Name)) {
						field.SetValue(null, settings[field.Name]);
					} else {
						field.SetValue(null, (string)field.GetCustomAttribute<SerializableSettingAttribute>(true).value);
					}
				} else if (field.FieldType == typeof(bool)) {
					if (settings.ContainsKey(field.Name)) {
						field.SetValue(null, settings[field.Name].ToLower() == "true");
					} else {
						field.SetValue(null, (bool)field.GetCustomAttribute<SerializableSettingAttribute>(true).value);
					}
				} else if (field.FieldType == typeof(int)) {
					if (settings.ContainsKey(field.Name)) {
						field.SetValue(null, int.Parse(settings[field.Name]));
					} else {
						field.SetValue(null, (int)field.GetCustomAttribute<SerializableSettingAttribute>(true).value);
					}
				} else if (field.FieldType == typeof(float)) {
					if (settings.ContainsKey(field.Name)) {
						field.SetValue(null, float.Parse(settings[field.Name]));
					} else {
						field.SetValue(null, (float)field.GetCustomAttribute<SerializableSettingAttribute>(true).value);
					}
				} else {
					Debug.WriteLine(string.Format("Setting {0} isn't of a supported type.", field.Name));
				}
				Debug.WriteLine(field.Name+": "+field.GetValue(null));
				return true;
			});
		}

		/// <summary>
		/// Saves the current settings into CONFIG_PATH.
		/// </summary>
		public static void Save() {
			List<String> lines = new List<String> ();
			SerializableSettingAttribute.GetSerializableFields(typeof(Settings)).All(property => {
				lines.Add(property.Name+"==="+property.GetValue(null));
				return true;
			});
			File.WriteAllLines(CONFIG_PATH.ToString(), lines);
		}

		public static void ReadConfigLines(string[] configLines, Action<string, string> callback) {
			foreach (string line in configLines) {
				string[] parts = line.Split("===".ToCharArray(), StringSplitOptions.RemoveEmptyEntries);
				if (parts.Length >= 2) {
					callback(parts[0], parts[1]);
				}
			}
		}

	}

	/// <summary>
	/// Marks the field as a serializable setting, so its value can be loaded from a serial representation.
	/// </summary>
	[AttributeUsage(AttributeTargets.Field)]
	public class SerializableSettingAttribute : System.Attribute {
		public object value;
		public SerializableSettingAttribute(object value) {
			this.value = value;
		}

		/// <summary>
		/// Gets the serializable fields of the given type.
		/// </summary>
		/// <returns>The fields marked with <see cref="SerializableSettingAttribute"/>.</returns>
		/// <param name="type">Type whose fields will be retreived.</param>
		public static IEnumerable<FieldInfo> GetSerializableFields(Type type) {
			return type.GetFields(BindingFlags.Static|BindingFlags.NonPublic).Where((prop) => {
				return prop.IsDefined(typeof(SerializableSettingAttribute), true);
			});
		}
	}
}

