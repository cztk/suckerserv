return {
server_start_message = "-> Successfully loaded Suckerserv",

client_connect = "(%{green}%{time}%{white}) Connection from %{yellow}%{country}%{white}: %{blue}%{name} %{magenta}(%{cn}) (%{priv})",
client_connect_admin = "IP: %{magenta}%{ip}",
client_disconnect = "(%{green}%{time}%{white}) Disconnected: %{blue}%{name}%{white} %{magenta}(%{cn})%{white}",
client_disconnect_admin = " (IP: %{magenta}%{ip})",

connect_info = "%{red}>>> %{yellow}Type %{magenta}#%{white}help for a %{blue}list %{white}of %{green}commands",

client_crcfail_player = "%{red}>>> %{blue}You %{white}are using a modified map!",
client_crcfail = "%{red}>>> %{white}Player %{blue}%{name} %{white}is using a modified map!",

clearbans = "%{red}>>> %{white}Cleared all %{blue}bans",

stats_disabled = "%{red}>>> %{orange}Stats are disabled for this match",
stats_enabled = "%{red}>>> %{green}Stats enabled",

stats_reload_disabled = "%{red}>>> Sorry, stats have been disabled for this match",

awards_stats = "%{red}>>> %{white}Awards: %{stats_message}",
awards_flags = "%{red}>>> %{white}Best Flags: %{flagstats_message}",

inactivitylimit = "%{red}>>> %{white}Server moved you to spectators, because you seem to be inactive - type '/spectator 0' to rejoin the game.",

command_disabled = "%{red}>>> ERROR: %{white}Command disabled",
command_permission_denied = "%{red}>>> ERROR: %{white}Permission Denied",

master_already = "%{red}>>> An admin or master is already here.",

demo_recording = "%{red}>>> %{white}Recording demo",

uptime = "%{red}>>> %{white}Server uptime: %{blue}%{uptime}",

help = "Command descriptions: #help <command>\n%{blue}List %{white}of %{green}commands%{white}",

stats_logged_in = "%{red}>>> %{white}You are logged in as %{blue}%{user_id}",

mapbattle_winner = "%{red}>>> %{white}Winner: %{blue}%{mapbattle_winner}",
mapbattle_vote = "%{red}>>> %{white}Vote for map %{blue}%{map1} %{white}or %{blue}%{map2} %{white}with %{green}1 %{white}or %{green}2",

}
