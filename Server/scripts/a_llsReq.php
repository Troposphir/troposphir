<?php
/*==============================================================================
  Troposphir - Part of the Troposphir Project
  Copyright (C) 2013  Troposphir Development Team

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as
  published by the Free Software Foundation, either version 3 of the
  License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
==============================================================================*/
if (!defined("INCLUDE_SCRIPT")) return;
class a_llsReq extends RequestResponse {
    static $fields = array(
        "isLOTD", "xpReward", "gms", "gmm", "gff", "ct", "ownerId",
        "gsv", "gbs", "gde", "gdb", "gctf", "gab", "gra", "gco",
        "gtc", "gmmp1", "gmmp2", "gmcp1", "gmcp2", "gmcdt",
        "gmcff", "ast", "aal", "ghosts", "ipad", "dcap", "dmic",
        "denc", "dpuc", "dcoc", "dtrc", "damc", "dphc", "ddoc",
        "dkec", "dgcc", "dmvc", "dsbc", "dhzc", "dmuc", "dtmi",
        "ddtm", "dttm", "dedc", "dtsc", "dopc", "dpoc", "deleted", "difficulty",
        "gmc", "draft", "version", "name", "description", "rating", "author"
    );

    protected function get_statement($json) {
        // The lucene2sql binary must be built from
        // https://github.com/Troposphir/lucene2sql/

        $lucene2sql = proc_open(
            dirname(realpath(__FILE__)) . "/lucene2sql",
            array(
                0 => array("pipe", "r"),
                1 => array("pipe", "w"),
                2 => array("pipe", "w")
            ),
            $pipes
        );

        if (!is_resource($lucene2sql)) {
            $this->error("INTERNAL");
            return;
        }

        fwrite($pipes[0], json_encode(array(
            "query" => $json["body"]["query"],
            "default_fields" => array("name", "description"),
            "allowed_fields" => $this::$fields,
            "renames" => array(
                "xp.level" => "xpLevel",
                "is.lotd" => "isLOTD",
                "xis.lotd" => "isLOTD",
                "xp.reward" => "xpReward",
                "xgmc" => "gmc",
                "xgmm" => "gmm",
                "xgms" => "gms",
                "xgco" => "gco",
                "xgtc" => "gtc",
                "xgctf" => "gctf",
                "xgab" => "gab",
                "xgra" => "gra",
                "designer" => "author"
            ),
            "expressions" => array(
                "xpLevel" => array(
                    array(true, "`xpReward` > 0"),
                    array(1, "`xpReward` > 0"),
                    array(false, "`xpReward` = 0"),
                    array(0, "`xpReward` = 0"),
                ),
                "deleted" => array(
                    array(null, "`deleted` = 0")
                ),
                "draft" => array(
                    array(null, "`draft` = 0")
                )
            ),
            "table" => $this->config['table_map']
        )));
        fclose($pipes[0]);

        $queryText = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        if (proc_close($lucene2sql) != 0) {
            $this->log($error);
            $this->error("INVALID");
            return;
        }

        $query = json_decode($queryText);
        $query->body = preg_replace("/;\$/", " ORDER BY `ct` DESC;", $query->body);

        $stmt = $this->getConnection()->prepare($query->body);
        foreach ($query->params as $param => $value) {
            $type = PDO::PARAM_STR;
            if (is_bool($value) || is_integer($value)) {
                $type = PDO::PARAM_INT;
            }

            $stmt->bindValue($param + 1, $value, $type);
        }

        return $stmt;
    }

    protected function validate_query($json) {
        return isset($json["body"]["query"]);
    }

    protected function row_to_level($row) {
        $level = array();

        foreach ($this::$fields as $field) {
            if ($field == 'deleted') continue;
            $level[$field] = $row[$field];
        }

        $level["id"]           = (string)$row["id"];
        $level["name"]         = (string)$row["name"];
        $level["description"]  = (string)$row["description"];
        $level["ownerId"]      = (string)$row["ownerId"];
        $level["dc"]           = (string)$row["dc"];
        $level["version"]      = (string)$row["version"];
        $level["draft"]        = (bool)$row['draft'] ? 'true' : 'false';
        $level["author"]       = (string)$row["author"];
        $level["editable"]     = (bool)$row['editable'] ? 'true' : 'false';
        $level['screenshotId'] = (string)$row['screenshotId'];
        $level['rating']       = (string)$row['rating'];
        $level['difficulty']   = (string)$row['difficulty'];
        $level['xgmc']         = (string)$row['gmc'];
        $level['xgmm']         = (string)$row['gmm'];
        $level['xgms']         = (string)$row['gms']; // fixes solo play bug

        $level["xis.lotd"]  = $level['isLOTD'];
        $level["is.lotd"]   = $level['isLOTD'];
        $level["xp.reward"] = $level['xpReward'];
        $level["xxp.reward"] = $level['xpReward'];

        unset($level['isLOTD']);
        unset($level['xpReward']);
        unset($level['xpLevel']);

        $props = array();
        $props["gcid"]     = (string)$row["gcid"];
        $props["editMode"] = (string)$row["editMode"];
        $level["props"]    = $props;

        return $level;
    }

    public function work($json) {
        //Check input
        if (!$this->validate_query($json)) return;
        if (!isset($json["body"]["freq"]["start"])) return;
        if (!is_numeric($json["body"]["freq"]["start"])) return;
        if (!is_numeric($json["body"]["freq"]["blockSize"])) return;

        $stmt = $this->get_statement($json);

        $stmt->execute();

        if ($stmt == false) {
            $this->error("NOT_FOUND");
            return;
        }

        $levelList = array();
        for ($count = 0; $row = $stmt->fetch(); $count++) {
            if ($count >= ($json['body']['freq']['start'] + $json['body']['freq']['blockSize'])) continue;
            if ($count < $json['body']['freq']['start']) continue;

            $levelList[] = $this->row_to_level($row);
        }
        $fres = array(
            "total"     => $count,
            "results"   => $levelList
        );
        $this->addBody("fres", $fres);
    }
}
?>
