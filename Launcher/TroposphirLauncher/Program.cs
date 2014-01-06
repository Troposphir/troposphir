using Microsoft.Win32;
using System;
using Gtk;

namespace TroposphirLauncher {
	class MainClass {
		public static void Main (string[] args) {
			RegisterURI();
			Application.Init ();
			MainWindow win = new MainWindow ();
			win.Show ();
			Application.Run ();
		}
		public static void RegisterURI() {
			#region Windows
			string programPath = System.Reflection.Assembly.GetExecutingAssembly().Location;
			RegistryKey key = Registry.ClassesRoot.CreateSubKey("Troposphir");
			key.SetValue("URL Protocol", "");
			key.SetValue("", "URL:Troposphir Protocol");
			key.CreateSubKey("DefaultIcon").SetValue("", programPath+", 1");
			key.CreateSubKey("shell").CreateSubKey("open").CreateSubKey("command").SetValue("", "\""+programPath+"\" \"%1\"");
			#endregion
			#region Mac
			//TODO: Go to work and make this there -Leonardo
			#endregion
		}
	}
}
