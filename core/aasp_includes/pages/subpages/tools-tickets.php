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


    global $GameServer, $GameAccount, $GamePage;
    $conn = $GameServer->connect();

    $GamePage->validatePageAccess("Tools->Tickets");
?>
<div class="box_right_title">Tickets</div>
<?php if (!isset($_GET['guid']))
    { ?>
    <table class="center">
        <tr>
            <td><input type="checkbox" id="tickets_offline">View Offline Tickets</td>
            <td>
                <select id="tickets_realm">
                    <?php
                    $GameServer->selectDB("webdb", $conn);

                    $result = $Database->select( char_db, name, description FROM realms;");
                    if ($result->num_rows == 0)
                    {
                        echo "<option value='NULL'>No Realms Found.</option>";
                    }
                    else
                    {
                        echo "<option value='NULL'>--Select A Realm--</option>";
                        while ($row = $result->fetch_assoc())
                        {
                            echo "<option value='". $row['char_db'] ."'>". $row['name'] ." - <i>". $row['description'] ."</i></option>";
                        }
                    }
                    ?>
                </select>
            </td>
            <td>
                <input type="submit" value="Load" onclick="loadTickets()">
            </td>
        </tr>
    </table>
    <hr/>
    <span id="tickets">
        <?php
        if (isset($_SESSION['lastTicketRealm']))
        {
            ##############################
            if ($GLOBALS['core_expansion'] == 3)
            {
                $guidString = "playerGuid";
            }
            else
            {
                $guidString = "guid";
            }

            ###############
            if ($GLOBALS['core_expansion'] == 3)
            {
                $closedString = "closed";
            }
            else
            {
                $closedString = "closedBy";
            }

            ###############
            if ($GLOBALS['core_expansion'] == 3)
            {
                $ticketString = "guid";
            }
            else
            {
                $ticketString = "ticketId";
            }
            ############################

            $offline = $_SESSION['lastTicketRealmOffline'];
            $realm   = $Database->conn->escape_string($_SESSION['lastTicketRealm']);


            if ($realm == "NULL")
            {
                die("<pre>Please Select A Realm.</pre>");
            }

            $Database->conn->select_db($realm);

            $result = $Database->select( ". $ticketString .", name, message, createtime, ". $guidString .", ". $closedString ." 
                FROM gm_tickets ORDER BY ticketId DESC;");
            if ($result->num_rows == 0)
            {
                die("<pre>No Tickets Were Found!</pre>");
            }

            echo "
			<table class='center'>
			   <tr>
				   <th>ID</th>
				   <th>Name</th>
				   <th>Message</th>
				   <th>Created</th>
				   <th>Ticket Status</th>
				   <th>Player Status</th>
				   <th>Quick Tools</th>
			   </tr>
			";

            while ($row = $result->fetch_assoc())
            {
                $get = $Database->select( COUNT(online) FROM characters WHERE guid='" . $row[$guidString] . "' AND online='1';");
                if ($get->data_seek(0) == 0 && $offline == "on")
                {
                    echo "<tr>";
                    echo "<td><a href='?page=tools&selected=tickets&guid=". $row[$ticketString] ."&database=". $realm ."'>". $row[$ticketString] ."</td>";
                    echo "<td><a href='?page=tools&selected=tickets&guid=". $row[$ticketString] ."&database=". $realm ."'>". $row['name'] ."</td>";
                    echo "<td><a href='?page=tools&selected=tickets&guid=". $row[$ticketString] ."&database=". $realm ."'>". substr($row['message'], 0, 15) ."...</td>";
                    echo "<td><a href='?page=tools&selected=tickets&guid=". $row[$ticketString] ."&database=". $realm ."'>". date('Y-m-d H:i:s', $row['createtime']) ."</a></td>";

                    if ($row[$closedString] == 1)
                    {
                        echo "<td><font color='red'>Closed</font></td>";
                    }
                    else
                    {
                        echo "<td><font color='green'>Open</font></td>";
                    }

                    $get = $Database->select( COUNT(online) FROM characters WHERE guid=". $row[$guidString] ." AND online=1;");
                    if ($get->data_seek(0) > 0)
                    {
                        echo "<td><font color='green'>Online</font></td>";
                    }
                    else
                    {
                        echo "<td><font color='red'>Offline</font></td>";
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
                    echo "<tr>";
                }
            }
            echo "</table>";
        }
        else
            echo "<pre>Please Select A Realm.</pre>";
        ?>
    </span>
    <?php
    }
    elseif (isset($_GET['guid']))
    {
        ##############################
        if ($GLOBALS['core_expansion'] == 3)
        {
            $guidString = "playerGuid";
        }
        else
        {
            $guidString = "guid";
        }

        ###############
        if ($GLOBALS['core_expansion'] == 3)
        {
            $closedString = "closed";
        }
        else
        {
            $closedString = "closedBy";
        }

        ###############
        if ($GLOBALS['core_expansion'] == 3)
        {
            $ticketString = "guid";
        }
        else
        {
            $ticketString = "ticketId";
        }
        ##############################

        $Database->conn->select_db($_GET['db']);
        $result = $Database->select( name, message, createtime, ". $guidString .", ". $closedString ." 
            FROM gm_tickets WHERE ". $ticketString ."='" . $Database->conn->escape_string($_GET['guid']) ."';");
        $row = $result->fetch_assoc();
        ?>
    <table style="width: 100%;" class="center">
        <tr>
            <td>
                <span class='blue_text'>Submitted By:</span>
            </td>	
            <td>
                <?php echo $row['name']; ?>
            </td>

            <td>
                <span class='blue_text'>Created:</span>
            </td>
            <td>
                <?php echo date("Y-m-d H:i:s", $row['createtime']); ?>
            </td>

            <td>
                <span class='blue_text'>Ticket Status:</span>
            </td>
            <td>
                <?php
                if ($row[$closedString] == 1)
                {
                    echo "<font color='red'>Closed</font>";
                }
                else
                {
                    echo "<font color='green'>Open</font>";
                }
                ?>
            </td>

            <td>
                <span class='blue_text'>Player Status:</span>
            </td>
            <td>
                <?php
                $get = $Database->select( COUNT(online) FROM characters WHERE guid=". $row[$guidString] ." AND online=1;");
                if ($get->data_seek(0) > 0)
                {
                    echo "<font color='green'>Online</font>";
                }
                else
                {
                    echo "<font color='red'>Offline</font>";
                }
                ?>
            </td>

        </tr>
    </table>
    <hr/>
    <?php
        echo nl2br($row['message']);
    ?>
    <hr/>
    <pre>
        <a href="?page=tools&selected=tickets">&laquo; Back to tickets</a>
        &nbsp; &nbsp; &nbsp;
        <a href="#" onclick="deleteTicket('<?php echo $_GET['guid']; ?>', '<?php echo $_GET['db']; ?>')">Remove Ticket</a>
        &nbsp; &nbsp; &nbsp;
    <?php 
        if ($row[$closedString] == 1)
        { ?>
		  <a href="#" onclick="openTicket('<?php echo $_GET['guid']; ?>', '<?php echo $_GET['db']; ?>')">Open Ticket</a>
        <?php
        }
        else
        {?>
            <a href="#" onclick="closeTicket('<?php echo $_GET['guid']; ?>', '<?php echo $_GET['db']; ?>')">Close Ticket</a>
        <?php
        }
    ?>
    </pre>
<?php
}