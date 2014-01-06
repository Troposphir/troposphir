using System;

namespace TroposphirLauncher {
	public partial class AlertWindow : Gtk.Window {
		public AlertWindow (string text) : base (Gtk.WindowType.Toplevel) {
			this.Build ();
			alertText.Text = text;
		}

		protected void Close (object sender, EventArgs e) {
			Destroy();
		}
	}
}

