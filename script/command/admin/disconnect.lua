--[[
Force disconnecting someone from the server (without kicking or banning him)
By LoveForever (C) 2011
]]--

return function(cn, tcn, ...)

	if not tcn then
		return false, "#disconnect <cn>"
	end
	
	if not server.valid_cn(tcn) then
	tcn = server.name_to_cn_list_matches(cn,tcn)

		if not tcn then
				return
		end
	end
	server.disconnect(tcn, 10, "disconnect by a admin")
end