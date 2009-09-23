### CAKEPHP CDN/Clould Front helper

Inspired by:
http://developer.amazonwebservices.com/connect/entry.jspa?externalID=2331

And RoR's asset host helper:
http://api.rubyonrails.org/classes/ActionView/Helpers/AssetTagHelper.html

(Please read the above link for a detailed explanation of what's going on here)

Instead of using Javascript or HTML helpers, you can use the provided Cf (Cloud Front) helper
to create links to assets, you can now use:

$cf->image();
$cf->jsLink();
$cf->css();

All options are exactly the same as default CakePHP helpers.

We make an assumption that local file system mirrors the remote one (on S3/Cloud Front/etc.)

The only settings you need to be concerned about are:

$assetHost = 'assets%d.example.com';
$numHostsMin = 0;
$numHostsMax = 3;
$sslHost = 'sslhost.example.com';

** Be sure to replace the above values in the helper to match your setup **

For any questions please contact me at the relevant blog post:
http://wp.me/peDIi-cJ

Your input is greatly appreciated. 