package com.onemoreblock.devlauncher;

public class Server
{
    private String name;
    private String url;
    
    public Server(String name, String url)
    {
        this.name = name;
        this.url = url;
    }
    
    public Server(String serializedServer)
    {
        name = "";
        url = "";
        
        boolean backslash = false;
        int mark = 0;
        for(int i = 0; i < serializedServer.length(); i++)
        {
            char c = serializedServer.charAt(i);
            
            boolean send = false;

            if(backslash)
            {
                send = true;
            }
            else if(c == ':')
            {
                mark++;
            }
            else if(c == '\\')
            {
                backslash = true;
            }
            else
            {
                send = true;
            }
            
            if(send)
            {
                if(mark == 0) name += c;
                else url += c;
            }
        }
    }
    
    public String getName() { return name; }
    public String getUrl() { return url; }
    
    public String serialize()
    {
        StringBuilder sb = new StringBuilder();
        
        // backslash backslashes and colens
        sb.append(name.replace("\\", "\\\\").replace(":", "\\:"));
        sb.append(":");
        sb.append(url.replace("\\", "\\\\").replace(":", "\\:"));
        
        return sb.toString();                
    }
}
