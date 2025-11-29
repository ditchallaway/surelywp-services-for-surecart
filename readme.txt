=== Services For SureCart ===

Contributors: Nelson
Tags: SurelyWP, SureCart, Services For SureCart
Requires at least: 6.2
Tested up to: 6.8.3
Stable tag: 1.7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin empowers you to sell services and custom deliverables with SureCart. Enjoy features like status and activity tracking, built-in messaging, and final delivery and approvals, all beautifully integrated directly into your website and customer dashboard.
SureCart Compatible up to 3.16.4

== Description ==

This plugin empowers you to sell services and custom deliverables with SureCart. Enjoy features like status and activity tracking, built-in messaging, and final delivery and approvals, all beautifully integrated directly into your website and customer dashboard.

= Installation =

* Unzip the downloaded zip file.
* Upload the plugin folder into the `wp-content/plugins/` directory of your WordPress site.
* Activate `Services For SureCart` from Plugins page

= Basic features =

* Integration with SureCart for seamless service management
* Create multiple services, each with their own customizable configuration and requirement form fields
* Associate SureCart products to each service by selecting all products, specific products, or products in specific collections
* Customize the title of each service for internal reference
* Enable or disable each service based on your operational needs
* Require digital signature for contract details before starting work
* Gather customer details efficiently using custom text input and file upload fields tailored to each service
* Customize service settings and preferences to align with your business requirements
* Redirect customer to the service after checkout
* Set a custom starting service ID number
* Manually mark services as started if customer shares requirement details externally
* Provide clear guidance to customers with titles and descriptions for each requirement field to help them understand what specific information or files are needed
* Control access to service features in the backend by assigning permissions based on user roles
* Set default delivery due dates for service orders to manage timelines effectively
* Implement custom order statuses such as "Waiting for Requirements" and "Work in Progress" to track service progress
* Customize email notification templates with personalized text and dynamic variables to enhance communication
* Utilize a messaging system within SureCart for direct communication between service providers and customers
* Facilitate easy file sharing features between service providers and customers
* Configure reminders and automatic order completion settings to streamline operational workflows
* Display product details and order details on the individual service for easy reference
* Manage client feedback effectively for the final delivery with an approval and revisions system
* Display pending alert notification badges in the menu for admins and customers
* Display pending services notice in the Customer Dashboard with custom Block and shortcode
* Display list of customer services with a custom Block or shortcode
* Display associated services in the SureCart Customer profile

Services For SureCart add a new submenu called "Services" under the "SurelyWP" menu. Here you are able to configure all the plugin settings.

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==
= 1.7.2 - November 17, 2025 =
Improvement: Removed The Final Delivery From The Status Tracker When The "Require Approval For Milestone Completion" Setting Is Disabled
Improvement: Removed The Word "Approved" From "Final Delivery Approved" In The Status Tracker
Fix: Ensured The Order Is Completed Without Customer Approval When The "Require Approval For Milestone Completion" Setting Is Disabled

= 1.7.1 - November 10, 2025 =
Fix: Resolved Issue With Dropdown Field Requirement Submissions Not Displaying On The Services Pages
Fix: Resolved Issue With Remaining Services Displayed When The "Require Approval" Setting Was Disabled

= 1.7 - October 30, 2025 =
New: Added Milestones Feature
New: Added Revisions Feature Within Milestones
New: Added Dropdown Select Field Type For Service Requirements
New: Added Status Badges To Each Services
New: Added Status Badges To Each Email Template

= 1.6.1 - September 18, 2025 =
Fix: Resolved An Occasional Error When Viewing Services In Backend
Fix: Fixed Uncaught Error On Update Page Due To Suggested Format In SureCart License System release.json

= 1.6 - September 10, 2025 =
Improvement: Refreshed The Plugin Settings Interface With A Polished Design For improved Usability And Modern Style
New: Added Feature To Export And Import Settings
Improvement: Updated The Services Table In The Customer Dashboard To Ensure Mobile Responsiveness
New: Added Setting To Enable/Disable The "Order Again" Button In The Individual Service Sidebar
Info: Renamed "Settings" Tab To "General Settings"
Fix: Updated The Code Related To Session Handling To Address A Site Health Notice 
Improvement: Updated All Possible Translation Strings In The Plugin

= 1.5.6 - June 12, 2025 =
New: Added "Settings" Link In WordPress Plugins List For Convenience
New: Added Ability To Permanently Delete Canceled Services 
Fix: Updated The Services Showing In The SureCart Menu After The Recent SureCart Update Changed Slugs With A New "Getting Started" Menu Link

= 1.5.5 - May 29, 2025 =
Improvement: Added Support To Automatically Create The First Service For A Subscription Order, Regardless Of The "Automatically Create A New Service For Each Billing Cycle" Setting

= 1.5.4 - Apr 28, 2025 =
Fix: Resolved The Issue With Headers Already Being Sent

= 1.5.3 - Mar 12, 2025 =
Fix: Resolved The File Download Issue On The Download Icon
Fix: Addressed The Admin Notice Conflict On The Settings Tab
Fix: Fixed The Service Creation Issue For Subscription Based Products

= 1.5.2 - Feb 27, 2025 =
Improvement: Added Hooks For The Service View

= 1.5.1 - Feb 11, 2025 =
New: Added Number Of Services Allowed Per Order Setting For One-Time Purchases And Installment Plans
New: Added Number Of Available Services Remaining In Main Services List
New: Added Number Of Remaining Services In Plan Details Section Individual Service Details
New: Added Ability To Rename Singular Word "Service" And Plural Word "Services" Throughout The Entire Interface For Customers And Admins To Make It Relevant For Custom Use Cases
New: Added Option To Replace Customer Dashboard Tab Icon
New: Added Customer Name To Order Details Section In Individual Services Admin View
Info: Updated Subscription Setting Terminology To Be Clear And Distinct For Subscription Based Services

= 1.5 - Jan 27, 2025 =
New: Added Subscription Services Feature
New: Added Shortcode To Display The Product's Submitted Requirements In The Checkout
New: Added Setting To Optionally Enable Or Disable The Auto Complete Order Feature
New: Added Setting To Customize The Services Tab Text In The Customer Dashboard
New: Added Services Column To The Main SureCart Orders Page
Improvement: Updated Requirements Fields Settings UI
Fix: Updated Placeholder Image Showing In Each Service With The Product Logo

= 1.4 - Jan 01, 2025 =
New: Show Service Requirements Form Fields On the Product Page
New: Display Service Requirments Form With Custom Block Or Shortcode
New: Added Text Input Field Type For Service Requirements Form
New: Added Required Option For Text Area Field
New: Added Setting To Optionally Show Rich Text Editor For Customer For Requirements Form Textarea
Improvement: Updated Setting Description On Product Page

= 1.3.3 - Dec 04, 2024 =
Improvement: Optimized Database Query Execution On Page Load
Fix: Addressed The Issue Of Excessive HTTP Requests
Fix: Corrected Styling Conflict With Rank Math SEO Plugin
Fix: Addressed Page Load Issue In The Customer Dashboard When The WordPress Default Theme Is Active
Fix: Fixed Service Requirements Save Issue When The Requirement Description Is Missing

= 1.3.2 - Nov 18, 2024 =
Fix: Setting Styling Issue In Product Page
Fix: Block Styling Issue In Order And Customer Page

= 1.3.1 - Oct 18, 2024 =
New: Added Support For User Switching In Toolkit For SureCart Addon
Improvement: Updated Code To Ensure Compatibility With SureCart 3
Improvement: Updated Translation For "Click Here" Link In Notification Emails
Improvement: Updated Error Message To Clarify That A Message Is Required When Sending Files In Messages
Improvement: Updated The Textarea Font
Improvement: Added Hooks For All Columns In Admin Services List
Fix: Resolved Issue With Multiple Messages Sending When Pressing Enter Multiple Times In A Message
Fix: Resolved Issue With Downloading Attachments In Notification Emails

= 1.3 - Sep 05, 2024 =
New: Added SureTriggers Integration
New: Added Recipient Email Addresses For Notification Emails
New: Added Filter Links By Status In Services List
New: Added TinyMCE Rich Text Editor And HTML Support In Messages
New: Made All Links Clickable Within Messages
New: Added TinyMCE Rich Text Editor And HTML Support In Contract Details
New: Added TinyMCE Rich Text Editor And HTML Support In Requirement Field Descriptions
New Added TinyMCE Rich Text Editor And HTML Support In Email Templates
New: Added Page ID Attribute To The Pending Services Alert Block And Shortcode
New: Added Confirmation Popup When Deleting Service
Improvement: Ensure Currency In Services Details Match SureCart Currency
Improvement: Updated The Textarea Font To Match Other Fields
Improvement: Updated Mobile Responsiveness
Improvement: Added Missing Translation Strings

= 1.2.3 - Aug 27, 2024 =
New: Added Separate Delivery Time Setting For Each Individual Service
New: Ability To Rearrange The Order Of Service Requirement Fields
New: Select Any File Type Supported By WordPress In The Allowed File Type Selector
New: Add Custom Mime Types For Supporting Additional Allowed File Types
Improvement: Changed Default Delivery Time Setting From Dropdown To Input Field To Allow Any Number Of Days
Info: Updated File Upload Size And Allowed File Types Setting Descriptions
Info: Added Service List Block And Shortcode Information In Settings

= 1.2.2 - July 29, 2024 =
Improvement: Add Page ID Attribute To Services List Block And Shortcode

= 1.2.1 - July 19, 2024 =
Fix: Cron Job For Services

= 1.2 - July 18, 2024 =
New: Redirect To Service After Checkout
New: Custom Starting Service ID Number
New: Manually Mark Services As Started
New: Display Product Variant Details In Individual Service
New: Set File Upload Fields As Optional
New: Display Associated Services On Customer Profile
New: Pending Service Notification Numbers
New: Display Pending Services Notice In Customer Dashboard
New: Pending Services Notice Block
New: Pending Services Notice Shortcode
Update: Show Product Details In Service Header
Update: show Order Details In Service Sidebar
Improvement: Align Services List Text To Left
Improvement: Rename Services List Block To "Services List"

= 1.1 - July 05, 2024 =
New: Added Feature For Optional Contract And Required Digital Signature
New: Added Customer Services Block
New: Added Customer Services Shortcode
Improvement: Updated Some UI Elements For Better SureCart Brand Color Support
Improvement: Updated Some UI Elements For Better SureCart Dark Mode Support
Improvement: Updated Terminology In Various Places

= 1.0.0 - June 18, 2024 =
* Initial Release