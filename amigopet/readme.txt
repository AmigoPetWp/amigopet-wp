=== AmigoPet ===
Contributors: wendelmax
Tags: adoption, animals, pet, shelter, nonprofit
Stable tag: 2.1.3
Requires at least: 6.2
Tested up to: 6.9
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Complete pet adoption management system for NGOs and animal shelters, with adoption contracts and QR codes for verification.

== External services ==

This plugin may connect to the following third-party services when the site administrator configures the corresponding options:

* **Google Maps (Geocoding API)** – Used to convert addresses to coordinates and coordinates to addresses (e.g. for location features). When used, the plugin sends the address or latitude/longitude provided by the user or by the organization’s settings. Configured via the plugin’s Google Maps API key. Google terms of service: https://policies.google.com/terms. Privacy policy: https://policies.google.com/privacy.

* **Google Charts (Image Charts)** – Used to generate QR code images for pet tracking. When a QR code is generated, the plugin sends the tracking URL to Google to produce the image. Google terms of service: https://policies.google.com/terms. Privacy policy: https://policies.google.com/privacy.

* **Signer.com API** – Used for electronic signing of adoption documents when this feature is enabled. Document data and signer information are sent according to the integration configuration. Signer.com terms and privacy policy apply; please refer to the provider’s documentation.

If you do not configure API keys or disable these features, the plugin does not send data to these services.

== Description ==

AmigoPet provides a complete solution for organizations working with animal adoption. With a modern and intuitive interface, the plugin manages the entire adoption process, from animal registration to post-adoption follow-up.

= Version 2.0.0 Highlights =

* Complete medical history system for pets
* Photo gallery system with thumbnails
* Enhanced multi-step adoption workflow
* Robust security system
* Database migrations system

= Main Features =

* User management system with multiple roles
* Complete medical history for pets with alerts
* Photo gallery with multiple upload
* Three-step adoption workflow
* Advanced permissions system
* Automatic database backup
* Dynamic terms system with placeholders
* Adoption terms preview printing

= Dynamic Terms System =

* Customizable terms for adoption, donation and volunteering
* Dynamic placeholders for automatic data insertion
* Adoption terms preview before visit
* Organization, adopter and pet data automatically filled
* User-friendly interface for editing terms
* Complete documentation of available placeholders

= Medical History System =

* Complete records of vaccines, exams and consultations
* Attachments for medical documents
* Veterinarian history
* Vaccination alerts
* Medical reports in PDF
* Advanced search by record type
* Filters by date and type
* Data export

= Photo Gallery System =

* Multiple photo upload
* Automatic thumbnail generation
* Featured profile photo
* Organization by albums
* Automatic image optimization
* Support for various formats
* Drag-and-drop interface
* Real-time preview

= Multi-Step Adoption System =

* Three-step process: documents → payment → approval
* Automatic requirements validation
* Notifications for adopters and organization
* Complete change history
* Customizable documents
* Approval system
* Real-time tracking
* Status reports

= Advanced Customization System =

* Responsive grid with 1-4 columns
* Three card styles: modern, classic and minimalist
* Complete color and typography customization
* Icon system for animal status
* Real-time preview of changes
* Configuration import and export
* Restore to default values
* Cache system for better performance

= User Role System =

* Administrator: Full system access
* Publisher: Can register and manage animals
* Adopter: Can apply for adoptions
* Role request and approval system

= Security and Performance =

* Complete data sanitization
* Nonce verification
* Role-based access control
* Form validation
* Configuration cache system
* Robust validation of imported data
* Configuration backup and restore

= Technical Features =

* WordPress 6.2+ compatibility
* Requires PHP 8.0+
* Custom migrations system
* TCPDF library for documents
* jQuery Validation Plugin (https://github.com/jquery-validation/jquery-validation) for form validation; update to the latest stable release (e.g. 1.22.x) when possible
* Advanced image processing
* Automatic backup system
* Rate limiting for APIs
* Access and action logging
* Modular and extensible architecture
* Static code analysis
* PSR-12 standards
* Automated tests

== External services ==

This plugin may connect to the following third-party services when the site administrator configures the corresponding options:

* **Google Maps (Geocoding API)** – Used to convert addresses to coordinates and coordinates to addresses (geocode and reverse geocode). The plugin sends the address or latitude/longitude to Google. Configured via the plugin's Google Maps API key. Google terms of service: https://policies.google.com/terms. Privacy policy: https://policies.google.com/privacy.

* **Google Charts (Image Charts)** – Used to generate QR code images for pet tracking. When a QR code is generated, the plugin sends the tracking URL to Google to produce the image. Google terms of service: https://policies.google.com/terms. Privacy policy: https://policies.google.com/privacy.

* **PetFinder API** – Used when the administrator configures the PetFinder API key. The plugin may request animal data from api.petfinder.com (e.g. for integration with the PetFinder platform). PetFinder's terms and privacy policy apply; see https://www.petfinder.com/developers/.

* **Signer.com API** – Used for electronic signing of adoption documents when this feature is enabled. Document content and signer name/email are sent to Signer.com (api.signer.com or sandbox). Signer.com terms and privacy policy apply; refer to the provider's documentation.

* **Payment gateway API** – Used for processing donations and adoption payments when the administrator configures the gateway API key and secret. The plugin sends payment data (amount, description, payment method, customer name/email/document) to the configured gateway. The provider's terms and privacy policy apply.

If you do not configure API keys or disable these features, the plugin does not send data to these services.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/amigopet-wp` folder
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure plugin options in 'AmigoPetWp > Settings'
4. Customize display in 'AmigoPetWp > Display Settings'

== Frequently Asked Questions ==

= Is the plugin compatible with any theme? =

Yes, the plugin was developed following WordPress best practices and is compatible with most modern themes.

= How do I customize the appearance of animal cards? =

Go to 'AmigoPetWp > Display Settings' to use our intuitive customization interface with real-time preview. You can adjust colors, typography, layout and more, all with instant visualization of changes.

= Can I backup my display settings? =

Yes! On the display settings page, you will find tools to export your current settings, import saved settings, and restore to default values when needed.

= How does the real-time preview work? =

The preview uses AJAX technology to instantly show how your changes will affect the appearance of animal cards, without reloading the page or saving the settings.

= Is the plugin optimized for performance? =

Yes! In addition to the cache system, we implemented:
* Automatic image optimization
* Asynchronous thumbnail generation
* Photo lazy loading
* API rate limiting
* Incremental database backup
* Background processing
* Complex query caching

== Screenshots ==

1. Main control panel
2. Customization interface with real-time preview
3. Adoption form
4. User and role management
5. Configuration import and export tools
6. Responsive preview of animal cards

== Changelog ==

= 2.1.3 =
* Version bump for WordPress.org package submission.
* Packaging adjustments for publishable ZIP distribution.

= 2.0.0 =
* Complete pet medical history system
* Photo gallery system with thumbnails
* Enhanced multi-step adoption workflow
* Robust security system
* Database migrations system
* Dependency and requirement updates
* Various performance improvements

= 1.1.0 =
* Added terms system with placeholders
* Adoption terms preview functionality
* User interface improvements
* Placeholder documentation
* Complete data replacement system in terms
* New print button on adoption form
* Layout optimized for term printing

= 1.0.0 =
* Initial plugin release
* Complete adoption management system
* Customization interface with real-time preview
* User role system
* Customizable forms
* Advanced settings system with import/export
* Configuration caching for better performance
* Robust data validation
* Responsive support
* Modern and intuitive interface

== Upgrade Notice ==

= 1.0.0 =
Initial version of the plugin with complete adoption management and advanced customization.

== Future Features ==

* Social network integration
* Donation system
* Advanced statistics and reports
* Expanded photo gallery
* Events and campaigns system
* More customization options
* Predefined themes for cards
* Advanced notification system
