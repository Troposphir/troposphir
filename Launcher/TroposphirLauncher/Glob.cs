using System;
using System.IO;
using System.Collections.Generic;

namespace TroposphirLauncher {
	/// <summary>
	/// Class that mimicks Python's <code>glob.glob()</code> function, returns a list
	/// of file paths with all files of all subfolders of the specified folder.
	/// </summary>
	public static class Glob {
		/// <summary>
		/// Lists all files under the provided folder, including files from all subfolders.
		/// </summary>
		/// <returns>The all files.</returns>
		/// <param name="rootPath">Root path from where list the files.</param>
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

