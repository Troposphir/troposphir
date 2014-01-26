using System;

namespace TroposphirLauncher {

	/// <summary>
	/// Dead-simple alert window, constructed by passing the alert text.
	/// </summary>
	public partial class AlertWindow : Gtk.Window {
		/// <summary>
		/// Initializes a new instance of the <see cref="TroposphirLauncher.AlertWindow"/> class.
		/// </summary>
		/// <param name="text">The alert text.</param>
		public AlertWindow (string text) : base (Gtk.WindowType.Toplevel) {
			this.Build ();
			alertText.Text = text;
		}

		protected void Close (object sender, EventArgs e) {
			Destroy();
		}
	}
}

