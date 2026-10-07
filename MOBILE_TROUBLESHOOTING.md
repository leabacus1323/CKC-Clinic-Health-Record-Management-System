# 🔧 Troubleshooting Mobile Access

## Problem: Can't Access from Mobile Phone

If your phone won't load the site when accessing `http://192.168.1.2:8000`, follow these steps:

---

## ✅ Solution 1: Allow Through Windows Firewall (REQUIRED)

### Step 1: Run PowerShell as Administrator
1. Press **Windows Key**
2. Type **"PowerShell"**
3. **Right-click** on "Windows PowerShell"
4. Select **"Run as Administrator"**
5. Click **"Yes"** on the security prompt

### Step 2: Run the Firewall Script
In the Administrator PowerShell window, run:

```powershell
cd "D:\Lea Bacus\ckc-shrms-master"
.\allow_mobile_access.ps1
```

**OR** manually run this command:

```powershell
New-NetFirewallRule -DisplayName "Laravel Dev Server Port 8000" -Direction Inbound -LocalPort 8000 -Protocol TCP -Action Allow -Profile Any
```

### Step 3: Verify the Rule Was Added
```powershell
Get-NetFirewallRule -DisplayName "Laravel Dev Server Port 8000"
```

You should see the rule listed.

---

## ✅ Solution 2: Temporary - Disable Windows Firewall (Quick Test)

**⚠️ Only for testing! Re-enable after testing.**

1. Open **Windows Security** (press Windows Key, type "Windows Security")
2. Click **"Firewall & network protection"**
3. Click your **active network** (Private network or Domain network)
4. Turn **OFF** "Microsoft Defender Firewall"
5. Try accessing from mobile again
6. **Turn it back ON** after testing

---

## ✅ Solution 3: Check Network Configuration

### Verify Both Devices Are on Same Network

**On Your Computer:**
```powershell
ipconfig
```
Look for your IPv4 Address (should be `192.168.1.2`)

**On Your Mobile:**
- Go to **WiFi Settings**
- Check connected WiFi network name
- Make sure it matches your desktop's WiFi/Router

### Verify Server is Running
In PowerShell, check if server is running:
```powershell
php artisan serve --host=0.0.0.0 --port=8000
```

You should see: `Server running on [http://0.0.0.0:8000]`

---

## ✅ Solution 4: Try Different Port

Sometimes port 8000 is blocked. Try port 8080 instead:

**Stop current server** (Ctrl+C), then run:
```powershell
php artisan serve --host=0.0.0.0 --port=8080
```

**Add firewall rule for port 8080:**
```powershell
New-NetFirewallRule -DisplayName "Laravel Dev Server Port 8080" -Direction Inbound -LocalPort 8080 -Protocol TCP -Action Allow -Profile Any
```

**Access from mobile:**
```
http://192.168.1.2:8080
```

---

## ✅ Solution 5: Check Router Settings

Some routers have **AP Isolation** or **Client Isolation** enabled, which prevents WiFi devices from communicating with LAN devices.

**To fix:**
1. Open your router admin panel (usually `http://192.168.1.1`)
2. Look for settings like:
   - **AP Isolation**
   - **Client Isolation**
   - **Wireless Isolation**
   - **SSID Isolation**
3. Make sure these are **DISABLED**
4. Save and restart router if needed

---

## ✅ Solution 6: Test Connection

### Test if Mobile Can Reach Your Computer

**On mobile browser, try:**
```
http://192.168.1.2
```

If this doesn't work at all, the problem is network connectivity, not Laravel.

### Ping Test (Advanced)

**On your computer:**
1. Find your mobile's IP address (check WiFi settings on phone)
2. Run in PowerShell:
```powershell
ping [mobile-ip-address]
```

If ping fails, there's a network isolation issue.

---

## 📋 Quick Checklist

- [ ] Server is running with `--host=0.0.0.0`
- [ ] Firewall rule added (or firewall temporarily disabled)
- [ ] Mobile and desktop on same network
- [ ] Using correct IP address (`192.168.1.2:8000`)
- [ ] Router AP Isolation is disabled
- [ ] No VPN running on desktop or mobile
- [ ] Port 8000 is not used by another application

---

## 🎯 Recommended Steps (In Order)

1. **Run firewall script as Administrator** ← START HERE
2. **Restart the server** with `--host=0.0.0.0`
3. **Try accessing from mobile**
4. If still doesn't work, **check router for AP Isolation**
5. If still doesn't work, **try different port (8080)**
6. Last resort: **temporarily disable firewall to test**

---

## 💡 After It Works

Once you can access from mobile:

1. **Re-enable Windows Firewall** (if you disabled it)
2. **Keep the firewall rule** (it's safe and specific to port 8000)
3. **Test PWA installation** on your mobile
4. **Remember**: Your computer must stay ON for mobile to access

---

## 🆘 Still Not Working?

**Check these:**
- Is your mobile using mobile data instead of WiFi?
- Is there a VPN active on phone or computer?
- Try restarting both computer and phone
- Try accessing from another device on the same WiFi

**For LAN-to-WiFi connectivity issues:**
- Your desktop (LAN) and mobile (WiFi) need to be on the same network subnet
- Most home routers handle this automatically, but some configurations may block it
- Check if router has "Guest Network" mode enabled on WiFi - this often isolates WiFi from LAN

---

**Most common solution: Run the firewall script as Administrator!**
