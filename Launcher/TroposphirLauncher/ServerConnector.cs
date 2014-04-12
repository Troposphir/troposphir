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
		/// Sends a HTTP request to the server under the given path.
		/// </summary>
		/// <param name="path">Server enpoint path.</param>
		public string Request(string path) {
			using (StreamReader reader = RawRequest(path, "", "application/json")) {
				return reader.ReadToEnd();
			}
		}

		public StreamReader RawRequest(string path, string data, string contentType) {
			string url = Path.Combine(ServerURL, path);
			HttpWebRequest request = (HttpWebRequest)WebRequest.Create(url);
			request.Method = "POST";
			request.ContentLength = data.Length;
			request.ContentType = contentType;
			request.UserAgent = "Troposphir Launcher Beta";
			Stream input = request.GetRequestStream();
			input.Write(new System.Text.ASCIIEncoding().GetBytes(data), 0, data.Length);
			input.Close();
			Stream output = request.GetResponse().GetResponseStream();
			output.ReadTimeout = 3000;
			return new StreamReader(output);
		}
	}
}

