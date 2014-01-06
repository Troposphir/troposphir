using System;
using System.IO;
using System.Collections.Generic;

namespace TroposphirLauncher {
	public static class Glob {
		public static List<string> ListAllFiles(string rootPath) {
			List<string> entries = new List<string>(Directory.GetFileSystemEntries(rootPath));
			List<string> result = new List<string>();
			foreach (string entry in entries) {
				if ((File.GetAttributes(entry) & FileAttributes.Directory) == FileAttributes.Directory) {
					result.AddRange(ListAllFiles(entry));
				} else {
					result.Add(entry);
				}
			}
			return result;
		}
	}
}

