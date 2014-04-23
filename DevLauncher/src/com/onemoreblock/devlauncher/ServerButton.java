package com.onemoreblock.devlauncher;

import javax.swing.JButton;

public class ServerButton extends JButton
{
    private static final long serialVersionUID = -2077262048683403918L;
    
    private Server server;
    public ServerButton(Server s)
    {
        this.server = s;
        setText(s.getName());
    }
    
    public Server getServer() { return server; }
}
