using System;
using System.Net;
using System.IO;

namespace TroposphirLauncher {
	/// <summary>
	/// Holds information of a server, and enables requests to that server.
	/// </summary>
	public class ServerConnector {
		/// <summary>
		/// The server's URL.
		/// </summary>
		public string ServerURL;
		public ServerConnector(string url) {
			ServerURL = url;
		}

		/// <summary>
		/// Sends a HTTP request to the server inder the given path.
		/// </summary>
		/// <param name="path">Server enpoint path.</param>
		public string Request(string path) {
			string url = Path.Combine(ServerURL, path);
			WebRequest request = WebRequest.Create(url);
			Stream data = request.GetResponse().GetResponseStream();
			data.ReadTimeout = 3000;
			using (StreamReader reader = new StreamReader(data)) {
				return reader.ReadToEnd();
			}
		}
	}
}

