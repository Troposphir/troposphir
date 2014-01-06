using System;

namespace TroposphirLauncher {
	public class UpdateItem {
		public string LocalPath;
		public string RemotePath;
		public UpdateItem(string local, string remote) {
			LocalPath = local;
			RemotePath = remote;
		}
	}
}

