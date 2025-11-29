<?php
/**
 * Changelogs
 *
 * @author  Surelywp
 * @package Services For SureCart
 * @version 1.0.0
 */

$changelogs = array(
	array(
		'version' => '1.7.2',
		'date'    => 'November 17, 2025',
		'items'   => array(
			'Improvement: Removed The Final Delivery From The Status Tracker When The "Require Approval For Milestone Completion" Setting Is Disabled',
			'Improvement: Removed The Word "Approved" From "Final Delivery Approved" In The Status Tracker',
			'Fix: Ensured The Order Is Completed Without Customer Approval When The "Require Approval For Milestone Completion" Setting Is Disabled',
		),
	),
	array(
		'version' => '1.7.1',
		'date'    => 'November 10, 2025',
		'items'   => array(
			'Fix: Resolved Issue With Dropdown Field Requirement Submissions Not Displaying On The Services Pages',
			'Fix: Resolved Issue With Remaining Services Displayed When The "Require Approval" Setting Was Disabled',
		),
	),
	array(
		'version' => '1.7',
		'date'    => 'October 30, 2025',
		'items'   => array(
			'New: Added Milestones Feature',
			'New: Added Revisions Feature Within Milestones',
			'New: Added Dropdown Select Field Type For Service Requirements',
			'New: Added Status Badges To Each Services',
			'New: Added Status Badges To Each Email Template',
		),
	),
	array(
		'version' => '1.6.1',
		'date'    => 'September 18, 2025',
		'items'   => array(
			'Fix: Resolved An Occasional Error When Viewing Services In Backend',
			'Fix: Fixed Uncaught Error On Update Page Due To Suggested Format In SureCart License System release.json',
		),
	),
	array(
		'version' => '1.6',
		'date'    => 'September 10, 2025',
		'items'   => array(
			'Improvement: Refreshed The Plugin Settings Interface With A Polished Design For improved Usability And Modern Style',
			'New: Added Feature To Export And Import Settings',
			'Improvement: Updated The Services Table In The Customer Dashboard To Ensure Mobile Responsiveness',
			'New: Added Setting To Enable/Disable The "Order Again" Button In The Individual Service Sidebar',
			'Info: Renamed "Settings" Tab To "General Settings"',
			'Fix: Updated The Code Related To Session Handling To Address A Site Health Notice ',
			'Improvement: Updated All Possible Translation Strings In The Plugin',
		),
	),
	array(
		'version' => '1.5.6',
		'date'    => 'June 12, 2025',
		'items'   => array(
			'New: Added "Settings" Link In WordPress Plugins List For Convenience',
			'New: Added Ability To Permanently Delete Canceled Services',
			'Fix: Updated The Services Showing In The SureCart Menu After The Recent SureCart Update Changed Slugs With A New "Getting Started" Menu Link',
		),
	),
	array(
		'version' => '1.5.5',
		'date'    => 'May 29, 2025',
		'items'   => array(
			'Improvement: Added Support To Automatically Create The First Service For A Subscription Order, Regardless Of The "Automatically Create A New Service For Each Billing Cycle" Setting',
		),
	),
	array(
		'version' => '1.5.4',
		'date'    => 'Apr 28, 2025',
		'items'   => array(
			'Fix: Resolved The Issue With Headers Already Being Sent',
		),
	),
	array(
		'version' => '1.5.3',
		'date'    => 'Mar 12, 2025',
		'items'   => array(
			'Fix: Resolved The File Download Issue On The Download Icon',
			'Fix: Addressed The Admin Notice Conflict On The Settings Tab',
			'Fix: Fixed The Service Creation Issue For Subscription Based Products',
		),
	),
	array(
		'version' => '1.5.2',
		'date'    => 'Feb 27, 2025',
		'items'   => array(
			'Improvement: Added Hooks For The Service View',
		),
	),
	array(
		'version' => '1.5.1',
		'date'    => 'Feb 11, 2025',
		'items'   => array(
			'New: Added Number Of Services Allowed Per Order Setting For One-Time Purchases And Installment Plans',
			'New: Added Number Of Available Services Remaining In Main Services List',
			'New: Added Number Of Remaining Services In Plan Details Section Individual Service Details',
			'New: Added Ability To Rename Singular Word "Service" And Plural Word "Services" Throughout The Entire Interface For Customers And Admins To Make It Relevant For Custom Use Cases',
			'New: Added Option To Replace Customer Dashboard Tab Icon',
			'New: Added Customer Name To Order Details Section In Individual Services Admin View',
			'Info: Updated Subscription Setting Terminology To Be Clear And Distinct For Subscription Based Services',
		),
	),
	array(
		'version' => '1.5',
		'date'    => 'Jan 27, 2025',
		'items'   => array(
			'New: Added Subscription Services Feature',
			'New: Added Shortcode To Display The Product\'s Submitted Requirements In The Checkout',
			'New: Added Setting To Optionally Enable Or Disable The Auto Complete Order Feature',
			'New: Added Setting To Customize The Services Tab Text In The Customer Dashboard',
			'New: Added Services Column To The Main SureCart Orders Page',
			'Improvement: Updated Requirements Fields Settings UI',
			'Fix: Updated Placeholder Image Showing In Each Service With The Product Logo',
		),
	),
	array(
		'version' => '1.4',
		'date'    => 'Jan 01, 2025',
		'items'   => array(
			'New: Show Service Requirements Form Fields On the Product Page',
			'New: Display Service Requirments Form With Custom Block Or Shortcode',
			'New: Added Text Input Field Type For Service Requirements Form',
			'New: Added Required Option For Text Area Field',
			'New: Added Setting To Optionally Show Rich Text Editor For Customer For Requirements Form Textarea',
			'Improvement: Updated Setting Description On Product Page',
		),
	),
	array(
		'version' => '1.3.3',
		'date'    => 'Dec 04, 2024',
		'items'   => array(
			'Improvement: Optimized Database Query Execution On Page Load',
			'Fix: Addressed The Issue Of Excessive HTTP Requests',
			'Fix: Corrected Styling Conflict With Rank Math SEO Plugin',
			'Fix: Addressed Page Load Issue In The Customer Dashboard When The WordPress Default Theme Is Active',
			'Fix: Fixed Service Requirements Save Issue When The Requirement Description Is Missing',
		),
	),
	array(
		'version' => '1.3.2',
		'date'    => 'Nov 18, 2024',
		'items'   => array(
			'Fix: Setting Styling Issue In Product Page',
			'Fix: Block Styling Issue In Order And Customer Page',
		),
	),
	array(
		'version' => '1.3.1',
		'date'    => 'Oct 18, 2024',
		'items'   => array(
			'New: Added Support For User Switching In Toolkit For SureCart Addon',
			'Improvement: Updated Code To Ensure Compatibility With SureCart 3',
			'Improvement: Updated Translation For "Click Here" Link In Notification Emails',
			'Improvement: Updated Error Message To Clarify That A Message Is Required When Sending Files In Messages',
			'Improvement: Updated The Textarea Font',
			'Improvement: Added Hooks For All Columns In Admin Services List',
			'Fix: Resolved Issue With Multiple Messages Sending When Pressing Enter Multiple Times In A Message',
			'Fix: Resolved Issue With Downloading Attachments In Notification Emails',
		),
	),
	array(
		'version' => '1.3',
		'date'    => 'Sep 05, 2024',
		'items'   => array(
			'New: Added SureTriggers Integration',
			'New: Added Recipient Email Addresses For Notification Emails',
			'New: Added Filter Links By Status In Services List',
			'New: Added TinyMCE Rich Text Editor And HTML Support In Messages',
			'New: Made All Links Clickable Within Messages',
			'New: Added TinyMCE Rich Text Editor And HTML Support In Contract Details',
			'New: Added TinyMCE Rich Text Editor And HTML Support In Requirement Field Descriptions',
			'New Added TinyMCE Rich Text Editor And HTML Support In Email Templates',
			'New: Added Page ID Attribute To The Pending Services Alert Block And Shortcode',
			'New: Added Confirmation Popup When Deleting Service',
			'Improvement: Ensure Currency In Services Details Match SureCart Currency',
			'Improvement: Updated The Textarea Font To Match Other Fields',
			'Improvement: Updated Mobile Responsiveness',
			'Improvement: Added Missing Translation Strings',
		),
	),
	array(
		'version' => '1.2.3',
		'date'    => 'Aug 23, 2024',
		'items'   => array(
			'New: Added Separate Delivery Time Setting For Each Individual Service',
			'New: Ability To Rearrange The Order Of Service Requirement Fields',
			'New: Select Any File Type Supported By WordPress In The Allowed File Type Selector',
			'New: Add Custom Mime Types For Supporting Additional Allowed File Types',
			'Improvement: Changed Default Delivery Time Setting From Dropdown To Input Field To Allow Any Number Of Days',
			'Info: Updated File Upload Size And Allowed File Types Setting Descriptions',
			'Info: Added Service List Block And Shortcode Information In Settings',
		),
	),
	array(
		'version' => '1.2.2',
		'date'    => 'July 29, 2024',
		'items'   => array(
			'Improvement: Add Page ID Attribute To Services List Block And Shortcode',
		),
	),
	array(
		'version' => '1.2.1',
		'date'    => 'July 19, 2024',
		'items'   => array(
			'Fix: Cron Job For Services',
		),
	),
	array(
		'version' => '1.2',
		'date'    => 'July 18, 2024',
		'items'   => array(
			'New: Redirect To Service After Checkout',
			'New: Custom Starting Service ID Number',
			'New: Manually Mark Services As Started',
			'New: Display Product Variant Details In Individual Service',
			'New: Set File Upload Fields As Optional',
			'New: Display Associated Services On Customer Profile',
			'New: Pending Service Notification Numbers',
			'New: Display Pending Services Notice In Customer Dashboard',
			'New: Pending Services Notice Block',
			'New: Pending Services Notice Shortcode',
			'Update: Show Product Details In Service Header',
			'Update: show Order Details In Service Sidebar',
			'Improvement: Align Services List Text To Left',
			'Improvement: Rename Services List Block To "Services List"',
		),
	),
	array(
		'version' => '1.1',
		'date'    => 'July 05, 2024',
		'items'   => array(
			'New: Added Feature For Optional Contract And Required Digital Signature',
			'New: Added Customer Services Block',
			'New: Added Customer Services Shortcode',
			'Improvement: Updated Some UI Elements For Better SureCart Brand Color Support',
			'Improvement: Updated Some UI Elements For Better SureCart Dark Mode Support',
			'Improvement: Updated Terminology In Various Places',
		),
	),
	array(
		'version' => '1.0.0',
		'date'    => 'June 21, 2024',
		'items'   => array(
			'Initial Release',
		),
	),
);

?>
<table class="form-table surelywp-ric-settings-box">
	<tbody>
		<tr class="surelywp-field-label">
			<td>
				<div class="change-log">
					<ul class="changelog-list">
						<?php foreach ( $changelogs as $log ) { ?>
							<li class="changelog-title">
								<div class="changelog-title-dots"><span></sspan></div>
								<div class="changelog-top">
									<h4 class="changelog-version"><?php echo esc_html( $log['version'] ); ?></h4>
									<div class="changelog-date"><?php echo esc_html( $log['date'] ); ?></div>
								</div>
								<ul class="changelog-sublist">
									<?php foreach ( $log['items'] as $item ) { ?>
										<li><?php echo esc_html( $item ); ?></li>
									<?php } ?>
								</ul>
							</li>
						<?php } ?>
					</ul>
				</div>
			</td>
		</tr>
	</tbody>
</table>