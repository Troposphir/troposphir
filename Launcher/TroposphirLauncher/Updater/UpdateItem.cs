using System;

namespace TroposphirLauncher {
	/// <summary>
	/// Represents a file that must be updated.
	/// </summary>
	public class UpdateItem {
		/// <summary>
		/// The local path of the file.
		/// </summary>
		public string LocalPath;
		/// <summary>
		/// The remote pathof the file.
		/// </summary>
		public string RemotePath;
		public UpdateItem(string local, string remote) {
			LocalPath = local;
			RemotePath = remote;
		}
	}
}

