<?php
    /* ___           __ _           _ __    __     _     
      / __\ __ __ _ / _| |_ ___  __| / / /\ \ \___| |__
      / / | '__/ _` | |_| __/ _ \/ _` \ \/  \/ / _ \ '_ \
      / /__| | | (_| |  _| ||  __/ (_| |\  /\  /  __/ |_) |
      \____/_|  \__,_|_|  \__\___|\__,_| \/  \/ \___|_.__/

      -[ Created by �Nomsoft
      `-[ Original core by Anthony (Aka. CraftedDev)

      -CraftedWeb Generation II-
      __                           __ _
      /\ \ \___  _ __ ___  ___  ___  / _| |_
      /  \/ / _ \| '_ ` _ \/ __|/ _ \| |_| __|
      / /\  / (_) | | | | | \__ \ (_) |  _| |_
      \_\ \/ \___/|_| |_| |_|___/\___/|_|  \__|	- www.Nomsoftware.com -
      The policy of Nomsoftware states: Releasing our software
      or any other files are protected. You cannot re-release
      anywhere unless you were given permission.
      � Nomsoftware 'Nomsoft' 2011-2012. All rights reserved. */

    define('INIT_SITE', TRUE);
    include "../../includes/misc/headers.php";
    include "../../includes/configuration.php";
    include "../functions.php";

    global $GameServer, $GameAccount;
    $conn = $GameServer->connect();

    $GameServer->selectDB("webdb");

    
    #                                                                   #
        ############################################################
    #                                                                   #
    if ( DATA['website']['core_expansion'] == 3 )
    {
        $guidString = 'playerGuid';
    }
    else
    {
        $guidString = 'guid';
    }

    if ( DATA['website']['core_expansion'] == 3 )
    {
        $closedString = 'closed';
    }
    else
    {
        $closedString = 'closedBy';
    }

    if ( DATA['website']['core_expansion'] == 3 )
    {
        $ticketString = 'guid';
    }
    else
    {
        $ticketString = 'ticketId';
    }
    

    # Organized Alphabeticaly


    switch ($_POST['action'])
    {

        case "closeTicket":
        {
            $id = $Database->conn->escape_string($_POST['id']);
            $db = $Database->conn->escape_string($_POST['db']);
            $Database->conn->select_db($db);

            $Database->conn->query("UPDATE gm_tickets SET ". $closedString ."=1 WHERE ". $ticketString ."=". $id .";");


            break;
        }

        case "delete":
        {
            $id = $Database->conn->escape_string($_POST['id']);

            $Database->conn->query("DELETE FROM realms WHERE id=". $id .";");

            $GameServer->logThis("Deleted a realm");

            break;
        }

        case "deleteTicket":
        {
            $id = $Database->conn->escape_string($_POST['id']);
            $db = $Database->conn->escape_string($_POST['db']);
            $Database->conn->select_db($db);

            $Database->conn->query("DELETE FROM gm_tickets WHERE ". $ticketString ."=". $id .";");

            break;
        }
        
        case "edit":
        {
            $id     = $Database->conn->escape_string($_POST['id']);
            $new_id = $Database->conn->escape_string($_POST['new_id']);
            $name   = $Database->conn->escape_string(trim($_POST['name']));
            $host   = $Database->conn->escape_string(trim($_POST['host']));
            $port   = $Database->conn->escape_string($_POST['port']);
            $chardb = $Database->conn->escape_string(trim($_POST['chardb']));

            if (empty($name) || empty($host) || empty($port) || empty($chardb))
                die("<span class='red_text'>Please enter all fields.</span><br/>");

            $GameServer->logThis("Updated realm information for " . $name);

            $Database->conn->query("UPDATE realms SET id=". $new_id .", name='". $name ."', host='". $host ."', port='". $port ."', char_database='". $chardb ."' 
                WHERE id=". $id .";");
            return TRUE;

            break;
        }
        
        case "edit_console":
        {
            $id   = $Database->conn->escape_string($_POST['id']);
            $type = $Database->conn->escape_string($_POST['type']);
            $user = $Database->conn->escape_string(trim($_POST['user']));
            $pass = $Database->conn->escape_string(trim($_POST['pass']));

            if (empty($id) || empty($type) || empty($user) || empty($pass))
            {
                die();
            }

            $GameServer->logThis("Updated console information for realm with ID: " . $id);

            $Database->conn->query("UPDATE realms SET sendType='". $type ."', rank_user='". $user ."', rank_pass='". $pass ."' WHERE id=". $id . ";");
            return TRUE;

            break;
        }
        
        case "getPresetRealms":
        {
            echo '<h3>Select a realm</h3><hr/>';
            $GameServer->selectDB("webdb");

            $result = $Database->select("realms", "id, name, description", null, null, "ORDER BY id ASC")->get_result();
            while ($row = $result->fetch_assoc())
            {
                echo '<table width="100%">';
                echo '<tr>';
                echo '<td width="60%">';
                echo '<b>' . $row['name'] . '</b>';
                echo '<br/>' . $row['description'];
                echo '</td>';

                echo '<td>';
                echo '<input type="submit" value="Select" onclick="savePresetRealm(' . $row['id'] . ')">';
                echo '</td>';
                echo '</tr>';
                echo '</table>';
                echo '<hr/>';
        }
            break;
        }
        
        case "loadTickets":
        {
            $offline = $Database->conn->escape_string($_POST['offline']);
            $realm   = $Database->conn->escape_string($_POST['realm']);

            $_SESSION['lastTicketRealm']        = $realm;
            $_SESSION['lastTicketRealmOffline'] = $offline;

            if ($realm == "NULL")
                die("<pre>Please select a realm.</pre>");

            $GameServer->selectDB($realm, $conn);

            $result = $Database->select("gm_tickets", "$ticketString, name, message, createtime, $guidString, $closedString", null, null, "ORDER BY ticketId DESC")->get_result();
            if ($result->num_rows == 0)
                die("<pre>No tickets were found!</pre>");

            echo "<table class='center'>
                   <tr>
                       <th>ID</th>
                       <th>Name</th>
                       <th>Message</th>
                       <th>Created</th>
                       <th>Ticket Status</th>
                       <th>Player Status</th>
                       <th>Quick Tools</th>
                   </tr>";

            while ($row = $result->fetch_assoc())
            {
                $get = $Database->select("characters", "COUNT(online)", null, "guid=$row[$guidString] AND online=1")->get_result();
                if ($get->data_seek(0) == 0 && $offline == "on")
                {
                    echo '<tr>';
                    echo '<td><a href="?page=tools&selected=tickets&guid=' . $row[$ticketString] . '&database=' . $realm . '">' . $row[$ticketString] . '</td>';
                    echo '<td><a href="?page=tools&selected=tickets&guid=' . $row[$ticketString] . '&database=' . $realm . '">' . $row['name'] . '</td>';
                    echo '<td><a href="?page=tools&selected=tickets&guid=' . $row[$ticketString] . '&database=' . $realm . '">' . substr($row['message'], 0, 15) . '...</td>';
                    echo '<td><a href="?page=tools&selected=tickets&guid=' . $row[$ticketString] . '&database=' . $realm . '">' . date('Y-m-d H:i:s', $row['createtime']) . '</a></td>';

                    if ($row[$closedString] == 1)
                    {
                        echo '<td><font color="red">Closed</font></td>';
                    }
                    else
                    {
                        echo '<td><font color="green">Open</font></td>';
                    }

                    $get = $Database->select("characters", "COUNT(online)", null, "guid=$row[$guidString] AND online=1")->get_result();
                    if ($get->data_seek(0) > 0)
                    {
                        echo '<td><font color="green">Online</font></td>';
                    }
                    else
                    {
                        echo '<td><font color="red">Offline</font></td>';
                    }
                    ?> <td><a href="#" onclick="deleteTicket('<?php echo $row[$ticketString]; ?>', '<?php echo $realm; ?>')">Delete</a>
                        &nbsp;
                        <?php if ($row[$closedString] == 1)
                        {
                            ?>
                            <a href="#" onclick="openTicket('<?php echo $row[$ticketString]; ?>', '<?php echo $realm; ?>')">Open</a>
                        <?php
                        }
                        else
                        {
                            ?>
                            <a href="#" onclick="closeTicket('<?php echo $row[$ticketString]; ?>', '<?php echo $realm; ?>')">Close</a>
                            <?php
                        }
                        ?>
                    </td><?php
                    echo '<tr>';
                }
            }
            echo '</table>';

            break;
        }
        
        case "openTicket":
        {
            $id = $Database->conn->escape_string($_POST['id']);
            $db = $Database->conn->escape_string($_POST['db']);
            $Database->conn->select_db($db);

            $Database->conn->query("UPDATE gm_tickets SET ". $closedString ."=0 WHERE ". $ticketString ."=". $id .";");

            break;
        }
        
        case "savePresetRealm":
        {
            $rid = $Database->conn->escape_string($_POST['rid']);

            if (isset($_COOKIE['presetRealmStatus']))
            {
                setcookie('presetRealmStatus', "", time() - 3600 * 24 * 30 * 3, '/');
                setcookie('presetRealmStatus', $rid, time() + 3600 * 24 * 30 * 3, '/');
            }
            else
            {
                setcookie('presetRealmStatus', $rid, time() + 3600 * 24 * 30 * 3, '/');
            }
            break;
        }
        
    }