<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: classes/SetPaths.php
//
// Auto detect and set the game path (uses the logic from setup_info)
// If it does not work, please comment this out and set it in db_config.php instead.
// But PLEASE also report that it did not work for you at the main BNT forums (forums.blacknova.net)

namespace Bnt;

class SetPaths
{
    public static function setGamepath()
    {
        $gamepath = dirname($_SERVER['PHP_SELF']);
        if (isset($gamepath) && strlen($gamepath) > 0)
        {
            if ($gamepath === "\\")
            {
                $gamepath = "/";
            }

            if ($gamepath[0] != ".")
            {
                if ($gamepath[0] != "/")
                {
                    $gamepath = "/$gamepath";
                }

                if ($gamepath[strlen($gamepath)-1] != "/")
                {
                    $gamepath = "$gamepath/";
                }
            }
            else
            {
                $gamepath ="/";
            }

            $gamepath = str_replace("\\", "/", stripcslashes($gamepath));
        }

        return $gamepath;
    }

    public static function setGamedomain()
    {
        $remove_port = true;
        $gamedomain = $_SERVER['HTTP_HOST'];

        if (isset($gamedomain) && strlen($gamedomain) >0)
        {
            $pos = strpos($gamedomain, "http://");
            if (is_int($pos))
            {
                $gamedomain = substr($gamedomain, $pos+7);
            }

            $pos = strpos($gamedomain, "www.");
            if (is_int($pos))
            {
                $gamedomain = substr($gamedomain, $pos+4);
            }

            if ($remove_port)
            {
                $pos = strpos($gamedomain, ":");
            }

            if (is_int($pos))
            {
                $gamedomain = substr($gamedomain, 0, $pos);
            }

            if ($gamedomain[0] != ".")
            {
                $gamedomain = ".$gamedomain";
            }
        }

        return $gamedomain;
    }
}
?>
