[![Code Climate](https://codeclimate.com/github/SpamExperts/plesk-extension/badges/gpa.svg)](https://codeclimate.com/github/SpamExperts/plesk-extension) [![Issue Count](https://codeclimate.com/github/SpamExperts/plesk-extension/badges/issue_count.svg)](https://codeclimate.com/github/SpamExperts/plesk-extension) [![Build Status](https://travis-ci.org/SpamExperts/plesk-extension.svg?branch=master)](https://travis-ci.org/SpamExperts/plesk-extension)

# plesk-extension

Plesk Extension for SpamExperts services integration.

### System requirements

- Odin Plesk Panel v12.5+
- PHP v5.6+

### Build (OSX/Linux)

After cloning the repo into a local copy, run the following commands:

```
cd <cloned sources>
chmod +x build.sh
./build.sh
```

### Install

 * Open Plesk
 * Log in as an administrator
 * Go to Server Management -> Extensions
 * Click Add Extension and upload your package

### Troubleshooting

The extension logs can bee seen at `/var/log/plesk/panel.log`. 

If your extension uses the Plesk GUI and it exits with an error or an exception, you should see the PHP stack trace. If you do not see it, and, say, see only a blank screen, try to improve the verbosity level of the debug output by adding a few lines to panel.ini. The file path is:
 
 * `(Linux) /usr/local/psa/admin/conf/panel.ini`
 * `(Windows) %plesk_dir%\admin\conf\panel.ini`
 
If you do not have this file, create it.
 
Open the file and add the following lines to it:
```
[log]
priority=7
```
