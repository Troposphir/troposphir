using System;
using System.IO;
using System.Linq;
using System.Collections.Generic;
using System.Security.Cryptography;
using System.Diagnostics;
using System.Threading;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;

namespace TroposphirLauncher {
	/// <summary>
	/// Handles all steps necessary to update the game and provides progress information.
	/// </summary>
	public class Updater {

		/// <summary>
		/// Container class that holds information necessary to the creation of a <see cref="Updater"/>.
		/// </summary>
		public class Config {
			public Action<UpdateStatus> StepChange { get; set; }
			public Action<float> Progress { get; set; }
			public string PatchFolder { get; set; }
			public ServerConnector Server { get; set; }
		}
		/// <summary>
		/// Defines the states of the update process
		/// </summary>
		public enum UpdateStatus {
			/// <summary>
			/// The <see cref="Updater"/>'s <code>Update()</code> method has not been called.
			/// </summary>
			NOT_STARTED,
			/// <summary>
			/// Updater is in the process of obtaining the update endpoint on the server.
			/// </summary>
			STARTING,
			/// <summary>
			/// Computing MD5 hashes of the game's files.
			/// </summary>
			CHECKING,
			/// <summary>
			/// Loading the remote hash list and comparing to the locally generated one to determine which files must be updated.
			/// </summary>
			COMPARING,
			/// <summary>
			/// Downloading the files determined out of date from the remote server to a temporary folder. Also verifies integrityu of downloaded files and redownloads if incorrect.
			/// </summary>
			DOWNLOADING,
			/// <summary>
			/// Replacing the game's files with the ones in the temporary folder and cleaning the tmporary folder.
			/// </summary>
			APPLYING,
			/// <summary>
			/// Update competed with success.
			/// </summary>
			DONE,
			/// <summary>
			/// An exception occurred and the update process has been aborted.
			/// </summary>
			FAILED
		}

		/// <summary>
		/// Gets the update status, as defined in <see cref="UpdateStatus"/>.
		/// </summary>
		/// <value>The status.</value>
		public UpdateStatus Status { get; private set; }
		/// <summary>
		/// The file hashes computed from local game files.
		/// </summary>
		Dictionary<string, string> fileHashes;
		/// <summary>
		/// The callback to invoke at each prgress change in any update step.
		/// </summary>
		Action<float> progressCallback;
		/// <summary>
		/// The callback to invoke when entering a different update step.
		/// </summary>
		Action<UpdateStatus> stepCallback;
		/// <summary>
		/// The server connector to use.
		/// </summary>
		ServerConnector server;
		/// <summary>
		/// The updater thread, where all work will be done when using <see cref="AsyncUpdate()"/> 
		/// </summary>
		Thread updater;
		/// <summary>
		/// The folder where the current game files reside and where to apply the patches.
		/// </summary>
		string patchFolder;
		/// <summary>
		/// The cancellation state of this updater. If it is marked as cancelled, it will not continue to the next step.
		/// </summary>
		bool cancelled = false;
		/// <summary>
		/// The update items that must be downloaded.
		/// </summary>
		List<UpdateItem> mustUpdate;

		/// <summary>
		/// Initializes a new instance of the <see cref="TroposphirLauncher.Updater"/> class using the configurations provided by the specified <see cref="Config"/>.
		/// </summary>
		/// <param name="config">The Updater Config to use.</param>
		public Updater(Config config) {
			server = config.Server;
			progressCallback = config.Progress;
			stepCallback = config.StepChange;
			patchFolder = config.PatchFolder;
			Status = UpdateStatus.NOT_STARTED;
		}


		/// <summary>
		/// Flags this updater for cancellation, so it will not proceed to the next step.
		/// </summary>
		public void Cancel() {
			cancelled = true;
		}

		/// <summary>
		/// Start the update process, which consists of getting a server endpoint, computing th local files' hashes, dumping them to a debug file, getting the remote hash list and comparing it to the locally generated one, then downloading the necessary files to finally replace the current files with them.
		/// </summary>
		public void Update() {
			fileHashes = new Dictionary<string, string>();
			bool stepSuccessful = true;
			if (stepSuccessful && !cancelled) stepSuccessful = TryUpdateStep(GetServerUpdateEndpoint, UpdateStatus.STARTING);
			if (stepSuccessful && !cancelled) stepSuccessful = TryUpdateStep(CheckFiles, UpdateStatus.CHECKING);
			if (stepSuccessful && MainClass.DebugMode && !cancelled) stepSuccessful = TryUpdateStep(DumpHashes, UpdateStatus.CHECKING);
			if (stepSuccessful && !cancelled) stepSuccessful = TryUpdateStep(LoadUpdates, UpdateStatus.COMPARING);
			if (stepSuccessful && !cancelled) stepSuccessful = TryUpdateStep(DownloadFiles, UpdateStatus.DOWNLOADING);
			if (stepSuccessful && !cancelled) stepSuccessful = TryUpdateStep(ApplyPatches, UpdateStatus.APPLYING);
			Status = UpdateStatus.DONE; stepCallback(Status);
		}

		/// <summary>
		/// Starts an asynchronous update task, see <see cref="Update()"/>.
		/// </summary>
		public void AsyncUpdate() {
			updater = new Thread(new ThreadStart(() => Update()));
			updater.IsBackground = true;
			updater.Start();
		}

		/// <summary>
		/// Tries to execute a update step, setting the Updater's Status to the given value, then running the step. If the step fails, Status becomes <see cref="UpdateStatus.FAILED"/>.
		/// </summary>
		/// <returns><c>true</c>, if update step was successful, <c>false</c> otherwise.</returns>
		/// <param name="task">Action to be run, takes a Action&lt;float&gt; parameter which is a progress callback, must be called with ranges 0.0 to 1.0.</param>
		/// <param name="targetState">Target state.</param>
		bool TryUpdateStep(Action<Action<float>> task, UpdateStatus targetState) {
			try {
				Status = targetState;
				stepCallback(Status);
				task(progressCallback);
				return true;
			} catch (Exception e) {
				Console.WriteLine(string.Format("Failed completing update step {0} with error \"{1}\": \n{2}", task.Method.Name, e.Message, e.StackTrace));
				Status = UpdateStatus.FAILED;
				return false;
			}
		}

		/// <summary>
		/// Gets the server update endpoint.
		/// </summary>
		/// <param name="progress">Progress.</param>
		void GetServerUpdateEndpoint(Action<float> progress) {
			progress(0);
			string response = server.Request("updater.php?starting");
			progress(1);
		}

		/// <summary>
		/// Computes the MD5 hashes of the local files.
		/// </summary>
		/// <param name="progress">Progress.</param>
		void CheckFiles(Action<float> progress) {
			using (MD5 md5 = MD5.Create()) {
				List<string> files = Glob.ListAllFiles(patchFolder);
				for (int index = 0, length = files.Count; index < length; index++) {
					progress(index / (float)(length - 1));
					string entry = Path.Combine(patchFolder, files[index]);
					using (FileStream stream = File.OpenRead(entry)) {
						fileHashes.Add(entry.Replace("\\", "/"), BitConverter.ToString(md5.ComputeHash(stream)));
					}
				}
			}
		}

		/// <summary>
		/// Dumps the local file hashes to a debug file.
		/// </summary>
		/// <param name="progress">Progress.</param>
		void DumpHashes(Action<float> progress) {
			List<string> lines = new List<string>();
			int index = 0;
			foreach (string key in fileHashes.Keys) {
				progress(index / (float)(fileHashes.Count - 1));
				string uri = Uri.UnescapeDataString(new Uri(patchFolder).MakeRelativeUri(new Uri(key)).ToString());
				lines.Add(uri.Replace("Atmosphir Dev/", "") + "===" + fileHashes[key]);
				index++;
			}
			File.WriteAllLines(Settings.HASHDUMP_PATH.FullName, lines);
		}

		/// <summary>
		/// Loads the remote hash lists and computes the files that must be updated
		/// </summary>
		/// <param name="progress">Progress.</param>
		void LoadUpdates(Action<float> progress) {
			string response = server.Request("updater.php?hashes&os="+GetOS());
			mustUpdate = new List<UpdateItem>();
			Debug.WriteLine(response);
			string[] lines = response.Split('\n');
			for (int index = 0, length = lines.Length; index < length; index++) {
				string[] parts = lines[index].Split("===".ToCharArray(), StringSplitOptions.RemoveEmptyEntries);
				if (parts.Length == 2) {
					string localPath = Path.Combine(new DirectoryInfo(patchFolder).FullName, parts[0]).Replace("\\", "/");
					FileInfo info = new FileInfo(localPath);
					string localHash = null;
					fileHashes.TryGetValue(localPath, out localHash);
					string remoteHash = parts[1].Replace("\r", "").Replace("\n","");
					if ((localHash == null || !localHash.Equals(remoteHash)) && info.Extension.ToLowerInvariant() != ".txt") {
						Debug.WriteLine("Must Update: "+localPath);
						mustUpdate.Add(new UpdateItem(localPath, parts[0]));
					}
				}
				progress(index / (float)(length - 1));
			}
		}

		/// <summary>
		/// Downloads the files that must be updated into a temporary folder.
		/// </summary>
		/// <param name="progress">Progress.</param>
		void DownloadFiles(Action<float> progress) {
			progress(0);
			if (mustUpdate != null && mustUpdate.Count > 0) {
				foreach (UpdateItem item in mustUpdate) {
					Debug.WriteLine(server.Request("updater.php?file="+item.RemotePath));
					using (StreamReader reader = server.RawRequest("updater.php?file=" + item.RemotePath + "&os=" + GetOS(), "", "")) {
						FileInfo tempFile = new FileInfo(Path.Combine(Settings.TEMP_PATH.FullName, item.GetHashCode().ToString()));
						using (StreamWriter writer = new StreamWriter(tempFile.Open(FileMode.Create))) {
							int i = reader.Read();
							Console.Write("Int "+i);
							writer.Write(i);
						}
						tempFile.Delete();
					}
				}
			}
			progress(1);
		}

		/// <summary>
		/// Moves the temporary files to the game folder, replacing the game's content in the process.
		/// </summary>
		/// <param name="progress">Progress.</param>
		void ApplyPatches(Action<float> progress) {
			progress(0);
		}

		/// <summary>
		/// Returns the normalized operating system name.
		/// </summary>
		/// <returns>The OS's name</returns>
		public static string GetOS() {
			switch (Environment.OSVersion.Platform) {
				case PlatformID.Win32NT:
				case PlatformID.Win32S:
				case PlatformID.Win32Windows:
				case PlatformID.WinCE:
					return "windows";
				case PlatformID.MacOSX:
					return "mac";
				default:
					return "windows";
			}
		}

	}
}

