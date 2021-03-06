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

class Server
{
    public function getRealmId($char_db)
    {
        global $Database;
        $Database->selectDB("webdb");

        $char_db = $Database->conn->escape_string($char_db);

        $statement = $Database->select("realms", "id", null, "char_db=$char_db");
        $result = $statement->get_result();
        $row = $result->fetch_assoc();

        return $row['id'];
        $statement->close();
    }

    public function getRealmName($char_db)
    {
        global $Database;
        $Database->selectDB("webdb");

        $char_db = $Database->conn->escape_string($char_db);

        $statement = $Database->select("realms", "name", null, "char_db=$char_db");
        $result = $statement->get_result();
        $row = $result->fetch_assoc();

        return $row['name'];
        $statement->close();
    }

    public function serverStatus($realm_id)
    {
        global $Database;

        $realm_id = $Database->conn->escape_string($realm_id);
        //Get status
        if ( @(fsockopen(DATA['realms'][$realm_id]['host'], DATA['realms'][$realm_id]['port'], $errno, $errstr, 1)) === false )
        {
            echo "<h4 class=\"text-center\">Offline</h4>";
        }
        else
        {
            echo $status = "<h4 class=\"text-center\">Online</h4>";

            echo "<span class='realm_status_text'>";

            /* Players online bar */
            if ( DATA['website']['server_status']['faction_bar'] == true )
            {
                $Database->selectDB('chardb', $realm_id);

                $statement = $Database->select("characters", "COUNT(online) AS online", null, "online=1");
                $result = $statement->get_result();
                $total_online = $result->fetch_assoc();
                $statement->close();

                if ( $total_online['online'] == 0 )
                {
                    $per_alliance = 50;
                    $per_horde    = 50;

                    $alliance['online'] = 0;
                    $horde['online']    = 0;
                }
                else
                {
                    $statement = $Database->select("characters", "COUNT(online) AS online", null, "online=1 AND race IN(3, 4, 7, 11, 1, 22)");
                    $getAlliance = $statement->get_result();
                    $alliance = $getAlliance->fetch_assoc();
                    $statement->close();

                    if ( $alliance['online'] == 0 || empty($alliance['online']) )
                    {
                        $per_alliance = 0;
                    }
                    else
                    {
                        $per_alliance = ($alliance['online'] / $total_online['online']) * 100;
                    }

                    $statement = $Database->select("characters", "COUNT(online) AS online", null, "online=1 AND race IN(2, 5, 6, 8, 10, 9)");
                    $getHorde = $statement->get_result();
                    $horde    = $getHorde->fetch_assoc();
                    $statement->close();
                    if ( $horde['online'] == 0 || empty($horde['online']) )
                    {
                        $per_horde = 0;
                    }
                    else
                    {
                        $per_horde = (($horde['online'] / $total_online['online']) * 100);
                    }
                }
                ?>
                <div class='srv_status_po'>
                    <div class='srv_status_po_alliance' style="width: <?php echo $per_alliance; ?>%;"></div>
                    <div class='srv_status_po_horde' style="width: <?php echo $per_horde; ?>%;"></div>
                    <div class='srv_status_text'>
                        <b style="color:blue;">Alliance: <?php echo $alliance['online']; ?></b>
                        &nbsp;
                        <b style="color:red;">Horde: <?php echo $horde['online']; ?></b>
                    </div>
                </div>
                <?php
            }

            echo "<table width='100%'><tr>";

            /** Get players online
            */
            if ( DATA['website']['server_status']['players_online'] == true )
            {
                $Database->selectDB('chardb', $realm_id);

                $statement = $Database->select("characters", "COUNT(online) AS online", null, "online=1");
                $getChars = $statement->get_result();
                $pOnline  = $getChars->fetch_assoc();
                $statement->close();
                if ( $pOnline['online'] > 1 || $pOnline['online'] == 0 ) 
                {
                    echo "<td><b>". $pOnline['online'] ."</b> Players Online</td>";
                }
                elseif ( $pOnline['online'] == 1 )
                {
                    echo "<td><b>". $pOnline['online'] ."</b> Player Online</td>";
                }
            }

            /** Get uptime
            */
            if ( DATA['website']['server_status']['uptime'] == true )
            {
                $Database->selectDB("logondb");
                $statement = $Database->select("uptime", "starttime", null, "realmid=$realm_id ORDER BY starttime DESC LIMIT 1");
                $getUp = $statement->get_result();
                $row   = $getUp->fetch_assoc();
                $statement->close();

                $time   = time();
                $uptime = $time - $row['starttime'];

                echo '
				<td>
				   <b>'. convTime($uptime) .'</b> uptime
				</td>
				</tr>';
            }
        }
        if ( DATA['website']['server_status']['next_arena_flush'] == true )
        {
            //Arena flush
            $Database->selectDB('chardb', $realm_id);
            
            $statement = $Database->select("worldstates", "value", null, "comment='NextArenaPointDistributionTime'");
            $row      = $getFlush->fetch_assoc();
            $flush    = date('d M H:i', $row['value']);
            $statement->close();

            echo '<tr>
		 	   <td>
			   	   Next arena flush: <b>' . $flush . '</b>
			   </td>';
        }
        echo '</tr>
	      </table>
		  </span>';
    }

    public function sendSoap($command, $username, $password, $host, $soapport)
    {

        $client = new SoapClient(NULL, 
            [
            "location" => "http://$host:$soapport/",
            "uri"      => "urn:TC",
            "style"    => SOAP_RPC,
            'login'    => $username,
            'password' => $password
            ]
        );
        try
        {
            $result = $client->executeCommand(new SoapParam($command, "command"));

            echo "Command succeeded! Output:<br />\n";
            echo $result;
        }
        catch (Exception $e)
        {
            echo "Command failed! Reason:<br />\n";
            echo $e->getMessage();
        }        
    }

    public function sendRA($command, $ra_user, $ra_pass, $server, $realm_port)
    {
        $telnet = @fsockopen($server, $realm_port, $error, $error_string, 3);
        if ( $telnet )
        {
            fgets($telnet, 1024);
            fputs($telnet, $ra_user . "\n");

            fputs($telnet, $ra_pass . "\n");

            fputs($telnet, $command . "\n");
            fclose($telnet);
        }
        else
        {
            die("Connection problems...Aborting | Error: $error_string");
        }
    }

}

$Server = new Server();