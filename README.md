# [Stanford Earth Saml](https://github.com/stanford-earth/stanford_earth_saml)
##### Version: 8.x-1.x-dev

Maintainers: [ksharp](https://github.com/ksharp-drupal)

Changelog: [Changelog.txt](CHANGELOG.txt)

Description
---

This module is used to limit simplesamlphp_auth logins to members of Stanford Workgroups and/or specific SUNet IDs. It also allows the option to automatically redirect to Stanford Weblogin when a 403 is returned for a page.

Accessibility
---
[![WCAG Conformance 2.0 AA Badge](https://www.w3.org/WAI/wcag2AA-blue.png)](https://www.w3.org/TR/WCAG20/)
Evaluation Date: 201X-XX-XX  
This module conforms to level AA WCAG 2.0 standards as required by the university's accessibility policy. For more information on the policy please visit: [https://ucomm.stanford.edu/policies/accessibility-policy.html](https://ucomm.stanford.edu/policies/accessibility-policy.html).

Installation
---

Install this module like any other module. [See Drupal Documentation](https://drupal.org/documentation/install/modules-themes/modules-8)

There is a patch needed to make the ReturnTo parameter work properly when doing autologin on 403s. The patch is found at: 
https://www.drupal.org/files/issues/simplesamlphp_auth-fix_return_to_as_get_parameter.patch

Configuration
---

Adds a Stanford Weblogin tab to the simplesamlphp_auth admin page. 

In that tab, list workgroups and/or SUNet IDs to limit who logs in, otherwise any valid user will log in. 

The tab also includes a checkbox for Auto Login on 403s. Checking the box and saving will set the Default 403 page on the site settings page.

Troubleshooting
---

If you are experiencing issues with this module try reverting the feature first. If you are still experiencing issues try posting an issue on the GitHub issues page.

Developer
---

If you wish to develop on this module you will most likely need to compile some new css. Please use the sass structure provided and compile with the sass compiler packaged in this module. To install:

```
npm install
grunt watch
 or
grunt devmode
```

Contribution / Collaboration
---

You are welcome to contribute functionality, bug fixes, or documentation to this module. If you would like to suggest a fix or new functionality you may add a new issue to the GitHub issue queue or you may fork this repository and submit a pull request. For more help please see [GitHub's article on fork, branch, and pull requests](https://help.github.com/articles/using-pull-requests)
