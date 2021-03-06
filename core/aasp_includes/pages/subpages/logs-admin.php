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


    global $GameServer, $GameAccount;
    $conn = $GameServer->connect();
    $GameServer->selectDB("webdb");

    $per_page = 20;

    $pages_query = $Database->select("admin_log", "COUNT(*) AS logs")->get_result();
    $pages       = ceil($pages_query->fetch_assoc()['logs'] / $per_page);

    $page  = ( isset($_GET['page']) ) ? $Database->conn->escape_string($_GET['page']) : 1;
    $start = ($page - 1) * $per_page;

    if (isset($_SESSION['cw_staff']) && !isset($_SESSION['cw_admin']))
    {
      if ($_SESSION['cw_staff_level'] < DATA['website']['admin']['minlvl'])
      {
        exit("Hey! You Shouldn't Be Here!");
      }
    }
?>
<div class="box_right_title">Admin log</div>
<table class="center">
    <tr>
      <th>Date</th>
      <th>User</th>
      <th>Action</th>
      <th>IP</th>
    </tr>
    <?php
        $GameServer->selectDB("webdb");
        $result = $Database->select("admin_log", null, null, null, "ORDER BY id DESC LIMIT $start, $per_page")->get_result();
        while ($row    = $result->fetch_assoc())
        { ?>
            <tr>
                <td><?php echo date("Y-m-d H:i:s", $row['timestamp']); ?></td>
                <td><?php echo $GameAccount->getAccName($row['account']); ?></td>
                <td><?php echo $row['action']; ?></td>
                <td><?php echo $row['ip']; ?></td>
            </tr>
    <?php } ?>
</table>
<hr/>
<?php
    if ($pages >= 1 && $page <= $pages)
    {
        if ($page > 1)
        {
            $prev = $page - 1;
            echo "<a href='?page=logs&selected=admin&log_page=". $prev ."' title='Previous'>Previous</a> &nbsp;";
        }
        for ($x = 1; $x <= $pages; $x++)
        {
            if ($page == $x)
            {
                echo "<a href='?page=logs&selected=admin&log_page=". $x ."' title='Page ". $x ."'><b>". $x ."</b></a> ";
            }
            else
            {
                echo "<a href='?page=logs&selected=admin&log_page=". $x ."' title='Page ". $x ."'>". $x ."</a> ";
            }
        }

        if ($page < $x - 1)
        {
            $next = $page + 1;
            echo "&nbsp; <a href='?page=logs&selected=admin&log_page=". $next ."' title='Next'>Next</a> &nbsp; &nbsp;";
        }
    }