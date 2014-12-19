<?php
/*
 * This is an example configuration for the mysql auth module.
 *
 * This SQL statements are optimized for following table structure.
 * If you use a different one you have to change them accordingly.
 * See comments of every statement for details.
 *
 * TABLE users
 *     uid   login   pass   firstname   lastname   email
 *
 * TABLE groups
 *     gid   name
 *
 * TABLE usergroup
 *     uid   gid
 *
 * To use this configuration you have to copy them to local.protected.php
 * or at least include this file in local.protected.php.
 */

/* Options to configure database access. You need to set up this
 * options carefully, otherwise you won't be able to access you
 * database.
 */
$conf['plugin']['authmysql']['server']   = 'localhost';
$conf['plugin']['authmysql']['user']     = 'DB USER';
$conf['plugin']['authmysql']['password'] = 'DB PASS';
$conf['plugin']['authmysql']['database'] = 'DB NAME';

/* This option enables debug messages in the mysql module. It is
 * mostly usefull for system admins.
 */
$conf['plugin']['authmysql']['debug'] = 0;

/* Normally password encryption is done by DokuWiki (recommended) but for
 * some reasons it might be usefull to let the database do the encryption.
 * Set 'forwardClearPass' to '1' and the cleartext password is forwarded to
 * the database, otherwise the encrypted one.
 */
$conf['plugin']['authmysql']['forwardClearPass'] = 0;

/* Multiple table operations will be protected by locks. This array tolds
 * the module which tables to lock. If you use any aliases for table names
 * these array must also contain these aliases. Any unamed alias will cause
 * a warning during operation. See the example below.
 */
$conf['plugin']['authmysql']['TablesToLock']= array("wp_users", "wp_users AS u","wp_groups_group", "wp_groups_group AS g", "wp_groups_user_group", "wp_groups_user_group AS ug");

/***********************************************************************/
/*       Basic SQL statements for user authentication (required)       */
/***********************************************************************/

/* This statement is used to grant or deny access to the wiki. The result
 * should be a table with exact one line containing at least the password
 * of the user. If the result table is empty or contains more than one
 * row, access will be denied.
 *
 * The module access the password as 'pass' so a alias might be necessary.
 *
 * Following patters will be replaced:
 *   %{user}    user name
 *   %{pass}    encrypted or clear text password (depends on 'encryptPass')
 *   %{dgroup}  default group name
 */
$conf['plugin']['authmysql']['checkPass']   = "SELECT user_pass AS pass
										 FROM wp_groups_user_group AS ug
										 JOIN wp_users AS u ON u.ID=ug.user_id
										 JOIN wp_groups_group AS g ON g.group_id=ug.group_id
                                         WHERE user_login='%{user}'
                                         LIMIT 1"; // This allows every user to log in.
                                         "AND name='Registered'"; // This limits to the "Registered" group.

/* This statement should return a table with exact one row containing
 * information about one user. The field needed are:
 * 'pass'  containing the encrypted or clear text password
 * 'name'  the user's full name
 * 'mail'  the user's email address
 *
 * Keep in mind that Dokuwiki will access thise information through the
 * names listed above so aliasses might be neseccary.
 *
 * Following patters will be replaced:
 *   %{user}    user name
 */
/*$conf['plugin']['authmysql']['getUserInfo'] = "SELECT password AS pass,
*						CONCAT(firstname,' ',lastname) AS name,
*						email AS mail
*                                        FROM bab_users
*                                         WHERE nickname='%{user}'";
*/
$conf['plugin']['authmysql']['getUserInfo'] = "SELECT user_pass AS pass,
						user_nicename AS name,
						user_email AS mail
                                         FROM wp_users
										 WHERE user_login='%{user}'";

/* This statement is used to get all groups a user is member of. The
 * result should be a table containing all groups the given user is
 * member of. The module access the group name as 'group' so a alias
 * might be nessecary.
 *
 * Following patters will be replaced:
 *   %{user}    user name
 */
$conf['plugin']['authmysql']['getGroups']   = "SELECT name as `group`
                                         FROM wp_groups_group g, wp_users u, wp_groups_user_group ug
                                         WHERE u.ID = ug.user_id
                                         AND g.group_id = ug.group_id
                                         AND u.user_login='%{user}'";

/***********************************************************************/
/*      Additional minimum SQL statements to use the user manager      */
/***********************************************************************/

/* This statement should return a table containing all user login names
 * that meet certain filter criteria. The filter expressions will be added
 * case dependend by the module. At the end a sort expression will be added.
 * Important is that this list contains no double entries fo a user. Each
 * user name is only allowed once in the table.
 *
 * The login name will be accessed as 'user' to a alias might be neseccary.
 * No patterns will be replaced in this statement but following patters
 * will be replaced in the filter expressions:
 *   %{user}    in FilterLogin  user's login name
 *   %{name}    in FilterName   user's full name
 *   %{email}   in FilterEmail  user's email address
 *   %{group}   in FilterGroup  group name
 */
$conf['plugin']['authmysql']['getUsers']    = "SELECT DISTINCT user_login AS user
                                         FROM wp_users AS u 
                                         LEFT JOIN wp_groups_user_group AS ug ON u.ID=ug.user_id
                                         LEFT JOIN wp_groups_group AS g ON ug.group_id=g.group_id";
$conf['plugin']['authmysql']['FilterLogin'] = "user_login LIKE '%{user}'";
$conf['plugin']['authmysql']['FilterName']  = "user_nicename LIKE '%{name}'";
$conf['plugin']['authmysql']['FilterEmail'] = "user_email LIKE '%{email}'";
$conf['plugin']['authmysql']['FilterGroup'] = "name LIKE '%{group}'";
$conf['plugin']['authmysql']['SortOrder']   = "ORDER BY user_login";

/***********************************************************************/
/*   Additional SQL statements to add new users with the user manager  */
/***********************************************************************/

/* This statement should add a user to the database. Minimum information
 * to store are: login name, password, email address and full name.
 *
 * Following patterns will be replaced:
 *   %{user}    user's login name
 *   %{pass}    password (encrypted or clear text, depends on 'encryptPass')
 *   %{email}   email address
 *   %{name}    user's full name
 */
/*
$conf['plugin']['authmysql']['addUser']     = "INSERT INTO users
                                         (login, pass, email, firstname, lastname)
                                         VALUES ('%{user}', '%{pass}', '%{email}',
                                         SUBSTRING_INDEX('%{name}',' ', 1),
                                         SUBSTRING_INDEX('%{name}',' ', -1))";
*/
/* This statement should add a group to the database.
 * Following patterns will be replaced:
 *   %{group}   group name
 */
/*
$conf['plugin']['authmysql']['addGroup']    = "INSERT INTO groups (name)
                                         VALUES ('%{group}')";
*/
/* This statement should connect a user to a group (a user become member
 * of that group).
 * Following patterns will be replaced:
 *   %{user}    user's login name
 *   %{uid}     id of a user dataset
 *   %{group}   group name
 *   %{gid}     id of a group dataset
 */
/*
$conf['plugin']['authmysql']['addUserGroup']= "INSERT INTO usergroup (uid, gid)
                                         VALUES ('%{uid}', '%{gid}')";
*/
/* This statement should remove a group fom the database.
 * Following patterns will be replaced:
 *   %{group}   group name
 *   %{gid}     id of a group dataset
 */
/*
$conf['plugin']['authmysql']['delGroup']    = "DELETE FROM groups
                                         WHERE gid='%{gid}'";
*/
/* This statement should return the database index of a given user name.
 * The module will access the index with the name 'id' so a alias might be
 * necessary.
 * following patters will be replaced:
 *   %{user}    user name
 */
$conf['plugin']['authmysql']['getUserID']   = "SELECT id
                                         FROM wp_users
                                         WHERE user_login='%{user}'";

/***********************************************************************/
/*   Additional SQL statements to delete users with the user manager   */
/***********************************************************************/

/* This statement should remove a user fom the database.
 * Following patterns will be replaced:
 *   %{user}    user's login name
 *   %{uid}     id of a user dataset
 */
/*
$conf['plugin']['authmysql']['delUser']     = "DELETE FROM users
                                         WHERE uid='%{uid}'";
*/
/* This statement should remove all connections from a user to any group
 * (a user quits membership of all groups).
 * Following patterns will be replaced:
 *   %{uid}     id of a user dataset
 */
/*
$conf['plugin']['authmysql']['delUserRefs'] = "DELETE FROM usergroup
                                         WHERE uid='%{uid}'";
*/
/***********************************************************************/
/*   Additional SQL statements to modify users with the user manager   */
/***********************************************************************/

/* This statements should modify a user entry in the database. The
 * statements UpdateLogin, UpdatePass, UpdateEmail and UpdateName will be
 * added to updateUser on demand. Only changed parameters will be used.
 *
 * Following patterns will be replaced:
 *   %{user}    user's login name
 *   %{pass}    password (encrypted or clear text, depends on 'encryptPass')
 *   %{email}   email address
 *   %{name}    user's full name
 *   %{uid}     user id that should be updated
 */
/* 
$conf['plugin']['authmysql']['updateUser']  = "UPDATE users SET";
$conf['plugin']['authmysql']['UpdateLogin'] = "login='%{user}'";
$conf['plugin']['authmysql']['UpdatePass']  = "pass='%{pass}'";
$conf['plugin']['authmysql']['UpdateEmail'] = "email='%{email}'";
$conf['plugin']['authmysql']['UpdateName']  = "firstname=SUBSTRING_INDEX('%{name}',' ', 1),
                                         lastname=SUBSTRING_INDEX('%{name}',' ', -1)";
$conf['plugin']['authmysql']['UpdateTarget']= "WHERE uid=%{uid}";
*/
/* This statement should remove a single connection from a user to a
 * group (a user quits membership of that group).
 *
 * Following patterns will be replaced:
 *   %{user}    user's login name
 *   %{uid}     id of a user dataset
 *   %{group}   group name
 *   %{gid}     id of a group dataset
 */
/*
$conf['plugin']['authmysql']['delUserGroup']= "DELETE FROM usergroup
                                         WHERE uid='%{uid}'
                                         AND gid='%{gid}'";
*/
/* This statement should return the database index of a given group name.
 * The module will access the index with the name 'id' so a alias might
 * be necessary.
 *
 * Following patters will be replaced:
 *   %{group}   group name
 */
$conf['plugin']['authmysql']['getGroupID']  = "SELECT id
                                         FROM wp_groups_group
                                         WHERE name='%{group}'";


