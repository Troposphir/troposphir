package com.onemoreblock.devlauncher;

import java.awt.BorderLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.WindowEvent;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.util.ArrayList;

import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JToggleButton;

public class DevLauncher extends JFrame implements ActionListener
{
    private static final long serialVersionUID = 2985686338302361164L;

    private static final String saveFile = "servers.dat";
    
    private JPanel paneServers = new JPanel();
    
    private JPanel paneButtons = new JPanel();
        private JButton btnAdd = new JButton("Add");
        private JToggleButton btnDel = new JToggleButton("Delete");
        
    ArrayList<ServerButton> servers = new ArrayList<ServerButton>();
        
    public DevLauncher()
    {
        setTitle("Atmosphir Dev Launcher");
        setSize(500, 200);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        
        btnAdd.addActionListener(this);
        
        paneButtons.add(btnAdd);
        paneButtons.add(btnDel);
        
        loadServers();
        
        this.setLayout(new BorderLayout());
        this.add(paneServers, BorderLayout.CENTER);
        this.add(paneButtons, BorderLayout.PAGE_END);
        
        setVisible(true);
    }
    
    public void addServer(String serializedServer)
    {
        addServer(new Server(serializedServer));
    }
    
    public void addServer(Server server)
    {
        ServerButton butt = new ServerButton(server);
        butt.addActionListener(this);
        paneServers.add(butt);
        servers.add(butt);
        
        validate();
        repaint();
        
        saveServers();
    }
    
    public void removeServer(ServerButton serverButton)
    {
        servers.remove(serverButton);
        paneServers.remove(serverButton);
        validate();
        repaint();                
        saveServers();        
    }
    
    public void loadServers()
    {
        File f = new File(saveFile);
        if(!f.exists()) return;
        
        try
        {
            BufferedReader reader = new BufferedReader(new InputStreamReader(new FileInputStream(f)));
            
            while(true)
            {
                String line = reader.readLine();
                if(line == null) break;
                addServer(line);
            }
            
            reader.close();
        }
        catch (FileNotFoundException e)
        {
            JOptionPane.showMessageDialog(this, "Error loading saved servers. File may be corrupted or protected.");
        }
        catch (IOException e)
        {
            JOptionPane.showMessageDialog(this, "Error loading saved servers. File may be corrupted or protected.");
        }
    }
    
    public void saveServers()
    {
        File f = new File(saveFile);
        if(f.exists()) f.delete();
        
        try
        {
            BufferedWriter writer = new BufferedWriter(new OutputStreamWriter(new FileOutputStream(f)));
            
            for(ServerButton sb : servers)
            {
                Server s = sb.getServer();
                writer.write(s.serialize());
                writer.newLine();
            }
            
            writer.flush();
            writer.close();
        }
        catch (FileNotFoundException e)
        {
            JOptionPane.showMessageDialog(this, "Error saving servers: " + e.getMessage());
        }
        catch (IOException e)
        {
            JOptionPane.showMessageDialog(this, "Error saving servers: " + e.getMessage());
        }
    }
    
    public void connect(Server s)
    {
        Runtime rt = Runtime.getRuntime();
        try
        {
            // set server to connect to
            File req = new File("Atmosphir_Data\\request.txt");
            BufferedWriter write = new BufferedWriter(new OutputStreamWriter(new FileOutputStream(req, false)));
            write.write(s.getUrl());
            write.newLine();
            write.write(s.getUrl());
            write.flush();
            write.close();
            
            // run atmosphir
            rt.exec("Atmosphir_Data\\Atmosphir.exe standalone");
            
            // close this window
            this.dispatchEvent(new WindowEvent(this, WindowEvent.WINDOW_CLOSING));
        }
        catch(Exception e)
        {
            JOptionPane.showMessageDialog(this, "ERROR: " + e.getMessage() + "\r\n"+e.getStackTrace());
        }
    }
    
    public static void main(String[] args)
    {
        new DevLauncher();
    }

    @Override
    public void actionPerformed(ActionEvent arg0)
    {
        Object s = arg0.getSource();
        
        if(s instanceof ServerButton)
        {
            ServerButton sb = (ServerButton)s;
            
            if(btnDel.isSelected())
            {
                btnDel.setSelected(false);
                removeServer(sb);
            }
            else
            {
                connect(sb.getServer());
            }
        }
        else if(s == btnAdd)
        {
            String name = JOptionPane.showInputDialog("Server name:");
            String url = JOptionPane.showInputDialog("Server url:");
            
            addServer(new Server(name, url));
        }
    }
}
