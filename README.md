#What is the Hierarchy Management Extension ? 
The hierarchy management extension is a horizonta(Infrastructure) extension by Techjoomla that allows you to set general or context specific hierarchial relations between users. This can be used by other extensions to manage special access control, generate reports and so on. 

Here is a live example of how general and context specific hierarchy can be. 

Mark, Swapnil, Mary & Vaishali normally report to Vijay. However Mary and Vaishali have been asked to additionally report to Parth in the special context of a 'Project A'

##How to Install
Donwload the code. Package it as a Zip and install it using the Joomla extension manager. 

##Configurtion
Integration Options : Core Joomla, EasySocial, JomSocial, EasyProfile, Community Builder

##Frontend Menus 
The Hierarchy management system supports the following menus on the frontend.

#My Team 
Shows a list view Of people directly reporting to the logged in user. Can be filtered by Context. Default is None. 
From this view they can drill down to the user and see a short User information page and also the team that reports to that user.An Icon will let you switch to a visual representation that will show a chart view. 

The default layout to use can be set in menu parameters. 

#Backend Views and Menus 
The following Views and menus are available on the backend. 

Options
Permissions
Manage Relations + Import/Export + set new relation

##Table Structure


* State - What will it be used for ? 
* Some sort of Adapters to query Human readable name for Context per extension

##Methods
SetHierarchy(userid,mgr_id,context,context_id)
GetHierarchyTree(userid,context,context_id,level)
GetManagers(userid,context,context_idlevel)

##Future Upgrades
*Fetching fields to show on User Team fiew from Integration available 
