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
class getLoadingTipReq extends RequestResponse {
	public $tips = array(
		"This server is hosted by OneMoreBlock. Come visit us at http://onemoreblock.com !",
		"You can check out the source code of this server at https://github.com/troposphir !",
		"Play Tip #19: No weapons, no problem - your character can fight bare-handed now!",
    "Trivia #6: The original Atmosphir concept only had basic platforms, moving platforms, falling platforms, ramps, ladders, and flags. Guess we got a little carried away.",
    "Play Tip # 4: The more background applications you close, the better Atmosphir will run!",
    "Play Tip #7: By default, 3000 points = 30 extra seconds. 10,000 points = 1 extra life. More time/lives/points = higher leaderboard scores.",
    "Play Tip #9: 0 counts as a life. So if the screen says you have 2 lives left, you really have 3. Video game math is weird like that.",
    "The Muka Tribe are intelligent creatures who have begun setting up camps, forging weapons, and attacking anything in their way.",
    "Design Tip #4: Q and E are shortcuts for moving up and down floors.",
    "Design Tip #8: R + scrollwheel = more fine-tuned object rotation. Shift + R = change rotation axis.",
    "Design Tip #10: Flip your entire level around with the Z and X keys. Overuse may result in nausea and dizziness.",
    "Trivia #8 In Czech, \"Muka\" means agony or torment.",
    "Play Tip #26: Run or jump powerups with their arrows filled in are only temporary.",
    "Play Tip #15: You can set up Atmosphir to work with a 3rd party controller in the options menu.",
    "Play Tip #28: If you touch the finish flag and it isn't finishing the level, you still have outstanding goals. Look in the lower-right corner for what you're missing.",
    "Design Tip #12: Tree trunk doors open from the ground, so only make locked rooms underneath them, not behind like other doors.",
    "Design Tip #27: Should the player find the flag, collect points, beat 20 enemies or all of the above? Set the level goals in the rulebook.",
    "Design Tip #6: Two heads are better than one! Make you levels editable so your friends can build on top of your creations.",
    "Design Tip #37: Want more plays? Make a YouTube trailer or walkthrough of your level and post it in the forums. Seeing a level in motion is always a great way to entice people to play it.",
    "Design Tip # 32: Use a mouse scrollwheel while holding the Q or E keys to slowly move through the space between floors.",
    "Play Tip #36: Make weapon-switching easier and faster! Assign each weapon to a number hotkey in the character creator.",
    "If a level thumbnail has LOTD (Level of the Day ) or XP (Experience) on its picture, that means you'll get experience points if you can beat it!",
    "Some say the flying skulls are spirits of former Atmosphir inhabitants, wandering the skies in search of new hosts...",
    "Design Tip #24: For music and skyboxes, if you press the \"pick area\" button you can pinpoint an area where battle music plays or the sky turns red.",
    "Design Tip #26: Save often. We're still in beta and you never know when something might go wrong.",
    "Play Tip #18: Press O or I to zoom the camera in or out while playing, or use the mouse scrollwheel.",
    "Play Tip #30: Cooperative levels do not count towards leaderboard scores. If you want to place, you'll have to play solo.",
    "Design Tip #17: Think about making your gravity shifters respawn, or players might get stuck in the wrong dimension!",
    "Play Tip #29: If you shoot right at the seams, sometimes you can blow up multiple breakable blocks at the same time.",
    "Design Tip #22: When making cooperative levels, think about separating players to take on different tasks. Triggering moving platforms is a great way for one player to help another make a bridge or staircase.",
    "Design Tip #16: Make a series of levels - if a player likes one, they're bound to try others too.",
    "Trivia #1: Atmosphir was first announced at the TechCrunch50 conference in San Francisco on September 10, 2008.",
    "Play Tip #17: If a moving platform is blinking, touch it or it will respawn back to its starting position.",
    "Sometimes, you have to think outside the box for some puzzles. Maybe a trap isn't all that it seems...",
    "Need some money to get that laser rifle? Play XP and LOTD levels to gain Atmos, Cirrus's main currency!"
	);
	public function work($json) {
		$this->addBody("categoryName", "");
		$this->addBody("tip", $this->tips[rand(0, count($this->tips)-1)]);
	}
}
?>
