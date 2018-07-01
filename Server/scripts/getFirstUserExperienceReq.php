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
include 'a_llsReq.php';

class getFirstUserExperienceReq extends a_llsReq {
	protected function get_statement($json) {
		$maps = $this->config["table_map"];
		$records = $this->config["table_playRecord"];

		$stmt = $this->getConnection()->prepare("
			SELECT m.*
			FROM `$maps` m
			INNER JOIN `$records` r  ON m.`id` = r.`levelId`
			WHERE
				`deleted` = 0
				AND `draft` = 0
				AND m.`rating` > 3
				AND m.`difficulty` > 3
			GROUP BY m.`id`
			HAVING (COUNT(IF(r.`state`='WON', 1, NULL)) / COUNT(*)) > 0.75
			ORDER BY m.`rating`;
		");

		return $stmt;
	}

	protected function validate_query($json) {
		return true;
	}

	protected function row_to_level($row) {
		$level = parent::row_to_level($row);

		$level["id"] 			= (integer)$row["id"];
		$level["draft"] 		= (bool)$row["draft"];
		$level["editable"] 		= (bool)$row["editable"];
		$level["ownerId"] 		= (integer)$row["ownerId"];
		$level["downloads"] 	= (integer)$row["downloads"];
		$level["version"] 		= (integer)$row["version"];
		$level["dataId"] 		= (integer)$row["dataId"];
		$level["screenshotId"] 	= (integer)$row["screenshotId"];

		return $level;
	}
}
?>