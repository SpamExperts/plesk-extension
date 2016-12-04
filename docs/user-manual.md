## Installing the extension

### System requirements

- Odin Plesk Panel v12.5+
- PHP v5.6+

### Setup instructions

The installation of the extension can be done from Plesk administrator interface. In order to install the extension the following steps have to be preformed:
 
- Download the installation package from the [Plesk extensions catalog](https://ext.plesk.com/packages/bbc5272e-7b88-4d5a-8a50-67431075fac4-spamexperts-extension)
- Log in as an administrator in Plesk and go to `Server Management` -> `Extensions` page
- Click `Add Extension` button
- Upload the installation package
- A success message saying `New extension was uploaded.` is expected on the screen as an indicator of successful installation

## Extension features for Plesk Administrators

For Plesk Administrators the extension is accessible from Plesk Home screen, `Professional SpamFilter` button:

![Spamexperts Plesk Extension Admin Home custom button](https://github.com/SpamExperts/plesk-extension/blob/master/docs/images/admin-home-custom-button.png)

### Configuration

The first action that needs to be accomplished before using the extension is configuration. If your instance is not configured you'll always be redirectd to the Configuration page.

The configuration page contains the full extension options and allows you to set up the extension to act as desired. In most cases, the default settings are sufficient (settings like SpamFilter API hostname and MX records still need to be filled in) but can be altered to suit your needs.

Please note: If you want to use this extension on SpamExperts' Hosted Cloud there are some [additional steps](https://my.spamexperts.com/kb/145/Using-addons-on-the-Hosted-Cloud.html) required.

### Settings

#### AntiSpam API URL

What URL is being used to manage the Spamfilter? This can be the primary hostname of your cluster, or a CNAME you're using. This URL will be validated. If this is not a SpamExperts Control Panel URL, you will be informed.

#### SpamFilter API hostname

The hostname being used to interface with the SpamFilter API. This is the hostname of your masterserver.

#### SpamFilter API username

The API username should be either an administrator or super admin account. We recommend you to use a separate admin user for each Plesk server.

#### SpamFilter API password

API password that belongs to the SpamFilter API user. The combination of hostname, username and password is being validated. If the login fails, you will be informed so you can make the appropriate adjustments.

#### Primary MX

The Primary MX record (MX10).

#### Secondary MX

The Secondary MX record (MX20)

#### Tertiary MX

The Tertiary MX record (MX30). Optional

#### Quaternary MX

The Quaternary MX record (MX40). Optional

#### Automatic action for a new domain when it is added to this server

If you want to automatically filter domains when adding new ones in Plesk, select the `Protect` action.

#### Automatic action for a domain when it is deleted from this server

Select the `Unprotect` action to automatically remove filtered domains when removing them from Plesk.

#### Action on the MX records for protected/unprotected domains

Select the `Update` action to automatically change the MX records for domains. This option uses the Primary/Secondary/Tertiary MX records to provision the DNS for a new domain.

#### Primary contact email for protected domains

Automatically set the contact address for the domain in the SpamExperts web interface. Using this, customers can use the "Retrieve login link" feature if they forget their password and will start receiving Protection Reports for their domain. For protection reports, the default settings are being used.

This function will work only if your account has an email-address attached in Plesk.

#### Action on secondary domains (domain aliases)

Use this option to control how the extension should handle domain aliases. If the `Protect as Domains` action is selected alias domains will be added as normal standalone domains. If you select the `Protect as Aliases` action, alias domains will be added as aliases for the root domain they belong to. If you don't want aliases to be handled at all select the `Skip` action.

#### Action on "remote" domains (hosted on external DNS servers)

This option allows to define the extension behavior for "remote" domains (a domain is considered as "remote" if it's DNS service does not host on current Plesk server but on some external one).

#### Redirect users upon logout

Select the `Back to Plesk` option in case you want to have the user redirected back to Plesk when they click the logout button in the SpamFilter interface. If the standard logout page of the SpamFilter interface should be used select the `To the SpamFilter panel logout page` option.

#### Action upon SpamFilter panel login to not protected domain

This function if the `Protect the domain and make another login attempt` option is selected will add the domain to the filter, in case the domain does not exist during login. This is useful to auto-protect domains during login, in case they are not protected yet.

#### Use as destination routes for clean mail when protecting domains

This options allows to manage what should be used as destination routes for protected domains - their existing MX hostnames or corresponding IP addresses.

### Domains

The domain list shows you all the panel domains and offers you an option to check if it is protected (if they are filtered by SpamExperts), change protection status (protect/unprotect) and to login to it. In order to run an operation for a single domain or a set of domain the domain(s) have to be selectd beforehand - there are checkboxes in each table row which are responsible for domains selection. It is possible to select/deselect all the page domains using the check box in the table header.

Please note: The selection is not persistent and cancels when switching pages. 

![Spamexperts Plesk Extension Admin Domains page](https://github.com/SpamExperts/plesk-extension/blob/master/docs/images/admin-domains-page.png)

### Branding

Using the branding option, you can change the appearance of the extension icon to match your own branding. This functionality is only available if you have purchased the Private Label (Whitelabel) or Premium Private Label (Premium whitelabel).

### Support

The support page shows you basic information about which versions are being used:

![Spamexperts Plesk Extension Admin Support page](https://github.com/SpamExperts/plesk-extension/blob/master/docs/images/admin-support-tab.png)
 
The parameters that the Supposr page shows contain data used by our support engineers to better help you. When asking support, please provide this information. 

### Troubleshooting

For more information please feel free to [contact support](mailto:support@spamexperts.com)

## Extension features for Plesk Resellers

For Plesk Resellers the extension is accessible from Plesk Home screen, `Professional SpamFilter` button:

![Spamexperts Plesk Extension Admin Home custom button](https://github.com/SpamExperts/plesk-extension/blob/master/docs/images/admin-home-custom-button.png)

### Domains

The domain list shows you all the domains that belong to current reseller and it's customers (Plesk v17+ only) and offers you an option to check if it is protected (if they are filtered by SpamExperts), change protection status (protect/unprotect) and to login to it. In order to run an operation for a single domain or a set of domain the domain(s) have to be selectd beforehand - there are checkboxes in each table row which are responsible for domains selection. It is possible to select/deselect all the page domains using the check box in the table header.

Please note: The selection is not persistent and cancels when switching pages. 

![Spamexperts Plesk Extension Reseller Domains page](https://github.com/SpamExperts/plesk-extension/blob/master/docs/images/admin-domains-page.png)

## Extension features for Plesk Customers

For Plesk Customers the extension is accessible from Plesk `Websites & Domains` screen, `Professional SpamFilter` link:

![Spamexperts Plesk Extension Websites &amp; Domains screen, Professional SpamFilter link](https://github.com/SpamExperts/plesk-extension/blob/master/docs/images/websites-and-domains-customer-link.png)

### Domains

The domain list shows you all the domains that belong to current customer and offers you an option to check if it is protected (if they are filtered by SpamExperts) and to login to it. In order to run an operation for a single domain or a set of domain the domain(s) have to be selectd beforehand - there are checkboxes in each table row which are responsible for domains selection. It is possible to select/deselect all the page domains using the check box in the table header.

Please note: The selection is not persistent and cancels when switching pages. 

![Spamexperts Plesk Extension Reseller Domains page](https://github.com/SpamExperts/plesk-extension/blob/master/docs/images/customer-domains-page.png)
