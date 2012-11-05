/*Array.prototype.inArray = function () { 
	var i; 
	for (i = 0; i < this.length; i++) { 
		if (this[i] === value) { return true; } 
	}; 
	return false; 
};*/  

String.prototype.format = function () {
    var args = arguments;
    return this.replace(/\{\{|\}\}|\{(\d+)\}/g, function (m, n) {
        if (m == "{{") { return "{"; }
        if (m == "}}") { return "}"; }
        return args[n];
    });
};

function timedRefresh(timeoutPeriod) {
    setTimeout("location.reload(true);", timeoutPeriod);
}

function markPlayers(dbData, opt, watch_list, last_update_cutoff_min) {
    players_displayed = 0;
    switch (opt) {
        case 1: //medical
            {
                for (i in dbData) {
                    if ((last_updated - dbData[i][4]) < last_update_cutoff_min * 60) {
                        try {
                            eval('medical=' + dbData[i][5] + ';');
                            death = (medical[0] == false) ? '' : '<br>DEAD';
                            unconscious = (medical[1] == false) ? '' : '<br>UNCONSCIOUS (' + Math.round(medical[10] / 60) + 'm ' + medical[10] % 60 + 's)';
                            infected = (medical[2] == false) ? '' : '<br>INFECTED';
                            injured = (medical[3] == false) ? '' : '<br>INJURED' + ((medical[8] != '') ? '<br>- ' + medical[8].join('<br>- ') : '');
                            inpain = (medical[4] == false) ? '' : '<br>IN PAIN';
                            heart = (medical[5] == false) ? '' : '<br>HEART FAILURE';
                            if (typeof (medical[9]) != 'undefined') {
                                broken = medical[9];
                                legs = (typeof (broken[0]) != 'undefined' && broken[0] > 0.900) ? '<br>BROKEN LEG (' + (Math.round(broken[0] * 10) / 10) + '%)' : '';
                                arms = (typeof (broken[0]) != 'undefined' && broken[1] > 0.900) ? '<br>BROKEN ARM (' + (Math.round(broken[1] * 10) / 10) + '%)' : '';
                            }
                            hunger = '';
                            thirst = '';
                            if (typeof (medical[11]) != 'undefined') {
                                energy = medical[11];
                                hunger = (typeof (energy[0]) != 'undefined' && energy[0] >= 1200) ? '<br>HUNGERY (' + Math.round(energy[0]) + ')' : '';
                                thirst = (typeof (energy[1]) != 'undefined' && energy[1] >= 1200) ? '<br>THIRSTY (' + Math.round(energy[1]) + ')' : '';
                            }
                            medsum = injured + inpain + unconscious + infected + legs + arms + heart + death + hunger + thirst;
                        } catch (err) {
                            alert('Error: Medical format for "' + dbData[i][0] + '", id "' + i + '" is not a valid array!');
                            medical = [];
                            medsum = '';
                        }
                        document.write('<div id="' + i + '" class="mark" style="' + getTopLeft(server_map, dbData[i][1], dbData[i][0], i) + ((dbData[i][2]) ? 'text-decoration:line-through;' : '') + 'z-index:' + dbData[i][4] + ';" oncontextmenu="thisHide(this,box_hide_delay);return false;"><b>' + dbData[i][0] + '</b><br><b>Humanity:</b> ' + Math.round(dbData[i][6]) + '<br><b>Blood:</b> ' + Math.round(medical[7] / 100) / 10 + 'U ' + ((medsum != '') ? '<br>' : '') + medsum + '</div>');
                        players_displayed++;
                    }
                }
            }
            break;
        case 2: //inventory
            {
                for (i in dbData) {
                    if ((last_updated - dbData[i][4]) < last_update_cutoff_min * 60) {
                        stuff = getStuff(dbData[i][7], dbData[i][8], watch_list, dbData[i][0], i);
                        document.write('<div id="' + i + '" class="mark" style="' + getTopLeft(server_map, dbData[i][1], dbData[i][0], i) + ((dbData[i][2]) ? 'text-decoration:line-through;' : '') + 'z-index:' + dbData[i][4] + ';" oncontextmenu="thisHide(this,box_hide_delay);return false;"><b>' + dbData[i][0] + '</b><br><br>' + dbData[i][9] + interpretState(dbData[i][10], dbData[i][0], i) + stuff + '</div>');
                        players_displayed++;
                    }
                }
            }
            break;
        case 3: //position
            {
                for (i in dbData) {
                    if ((last_updated - dbData[i][4]) < last_update_cutoff_min * 60) {
                        document.write('<div id="' + i + '" class="mark" style="' + getTopLeft(server_map, dbData[i][1], dbData[i][0], i) + ((dbData[i][2]) ? 'text-decoration:line-through;' : '') + 'z-index:' + dbData[i][4] + ';" oncontextmenu="thisHide(this,box_hide_delay);return false;"><b>' + dbData[i][0] + '</b><br><input type="text" style="font-size:9px;" onclick="this.focus();this.select();" value="' + dbData[i][11] + '"></div>');
                        players_displayed++;
                    }
                }
            }
            break;
        default:
            {
                for (i in dbData) {
                    if ((last_updated - dbData[i][4]) < last_update_cutoff_min * 60) {
                        document.write('<div id="' + i + '" class="mark" style="' + getTopLeft(server_map, dbData[i][1], dbData[i][0], i) + ((dbData[i][2]) ? 'text-decoration:line-through;' : '') + 'z-index:' + dbData[i][4] + ';" oncontextmenu="thisHide(this,box_hide_delay);return false;"><b>' + dbData[i][0] + '</b> [<a class="lnk" href="dz_player_history_bliss.php?guid=' + i + '">' + i + '</a>]<br>' + dbData[i][3] + '</div>');
                        players_displayed++;
                    }
                }
            }
    }
    document.getElementById('players').value += ' (' + players_displayed + ')';
}

function markTents(dbData, opt, watch_list) {
    switch (opt) {
        case 1: //inventory
            {
                for (i in dbData) {
                    stuff = getStuff2(dbData[i][5], watch_list, dbData[i][0], i);
                    document.write('<div id="' + i + '" class="mark" style="' + getTopLeft(server_map, dbData[i][1], dbData[i][0], i) + 'z-index:' + dbData[i][4] + ';" oncontextmenu="thisHide(this,box_hide_delay);return false;"><b>' + dbData[i][0] + '</b> [' + i + ']<br>Owner: <b>' + dbData[i][2] + '</b> [' + dbData[i][6] + '] oid: [' + dbData[i][7] + ']<br>' + dbData[i][3] + stuff + '</div>');
                }
            }
            break;
        case 2: //position
            {
                for (i in dbData) {
                    document.write('<div id="' + i + '" class="mark" style="' + getTopLeft(server_map, dbData[i][1], dbData[i][0], i) + 'z-index:' + dbData[i][4] + ';" oncontextmenu="thisHide(this,box_hide_delay);return false;"><b>' + dbData[i][0] + '</b> [' + i + ']<br>Owner: <b>' + dbData[i][2] + '</b> [' + dbData[i][6] + '] oid: [' + dbData[i][7] + ']<br>' + dbData[i][3] + '<br><input type="text" style="width:155px;font-size:10px;" onclick="this.focus();this.select();" value="' + dbData[i][1] + '"></div>');
                }
            }
            break;
        default:
            {
                for (i in dbData) {
                	// '$worldspace','$last_updated','$timestamp','$owner_id','$owner_name','$inventory'
                    document.write('<div id="' + i + '" class="mark" style="' + getTopLeft(server_map, dbData[i][0], "TentStorage", i) + 'z-index:' + dbData[i][2] + ';" oncontextmenu="thisHide(this,box_hide_delay);return false;"><b>Tent</b> [' + i + ']<br>Owner: <b>' + dbData[i][3] + '</b> [' + dbData[i][4] + ']<br>' + dbData[i][5] + '</div>');
                }
            }
    }
}

function markVehicles(dbData, opt, watch_list, filter) {
    selected_vehicles = 0;
    switch (opt) {
        case 1: // damages
            {
                for (i in dbData) {
                    damage = Math.round((1 - dbData[i][2]) * 100);
                    document.write('<div id="' + i + '" class="mark" style="' + getTopLeft(server_map, dbData[i][1], dbData[i][0], i) + 'z-index:' + dbData[i][4] + ';" oncontextmenu="thisHide(this,box_hide_delay);return false;"><b>' + dbData[i][0] + '</b> [' + i + ']<br><b>Health:</b> ' + damage + '%' + '<br><b>Fuel: </b>' + Math.round((dbData[i][5] * 100)) + '%' + getDamage(dbData[i][6], dbData[i][0], i) + '</div>');
                    selected_vehicles++;
                }
            }
            break;
        case 2: // inventory
            {
                for (i in dbData) {
                    stuff = getStuff2(dbData[i][7], watch_list, dbData[i][0], i);
                    document.write('<div id="' + i + '" class="mark" style="' + getTopLeft(server_map, dbData[i][1], dbData[i][0], i) + 'z-index:' + dbData[i][4] + ';" oncontextmenu="thisHide(this,box_hide_delay);return false;"><b>' + dbData[i][0] + '</b> [' + i + ']' + stuff + '</div>');
                    selected_vehicles++;
                }
            }
            break;
        case 3: // position
            {
                for (i in dbData) {
                    document.write('<div id="' + i + '" class="mark" style="' + getTopLeft(server_map, dbData[i][1], dbData[i][0], i) + 'z-index:' + dbData[i][4] + ';" oncontextmenu="thisHide(this, box_hide_delay); return false;"><b>' + dbData[i][0] + '</b> [' + i + ']<br><input type="text" style="font-size:9px;" onclick="this.focus(); this.select();" value="' + dbData[i][8] + '"></div>');
                    selected_vehicles++;
                }
            }
            break;
        default:
            {
                all_vehicles = new Array();
                
                for (i in dbData) {
                    if (filter == dbData[i][0] || filter == '') {
                        document.write('<div id="' + i + '" class="mark" style="' + getTopLeft(server_map, dbData[i][1], dbData[i][0], i) + 'z-index:' + dbData[i][4] + ';" oncontextmenu="thisHide(this,box_hide_delay);return false;"><b>' + dbData[i][0] + '</b> [' + i + ']<br>' + dbData[i][3] + '</div>');
                        selected_vehicles++;
                    }
                
                    all_vehicles[dbData[i][0]] = dbData[i][0];
                }
                
                filter_list = '';
                
                for (i in all_vehicles) {
                    filter_list += '&nbsp;<a href="?' + i + '" class="' + ((filter == i) ? 'flnk-s' : 'flnk') + '">' + i + '</a>';
                }
                
                document.getElementById('filter').innerHTML = '<br><b>Filter:</b>&nbsp;<a href="?" class="' + ((filter == '') ? 'flnk-s' : 'flnk') + '">All</a>' + filter_list;
                document.getElementById('vehicles').value = 'Vehicles (' + selected_vehicles + ')';
            }
    }
}

function markObjects(dbData, filter) {
    all_objects = new Array();
    selected_objects = 0;
    for (i in dbData) {
        if (filter == dbData[i][0] || filter == '') {
            document.write('<div id="' + i + '" class="mark" style="' + getTopLeft(server_map, dbData[i][1], dbData[i][0], i) + 'z-index:' + dbData[i][4] + ';" oncontextmenu="thisHide(this, box_hide_delay); return false;"><b>' + dbData[i][0] + '</b> [' + i + ']<br>Owner: <b>' + dbData[i][2] + '</b> [' + dbData[i][5] + '] oid: [' + dbData[i][6] + ']<br>' + dbData[i][3] + '</div>');
            selected_objects++;
        }
        all_objects[dbData[i][0]] = dbData[i][0];
    }
    filter_list = '';
    for (i in all_objects) {
        filter_list += '&nbsp;<a href="?' + i + '" class="' + ((filter == i) ? 'flnk-s' : 'flnk') + '">' + i + '</a>';
    }
    document.getElementById('filter').innerHTML = '<br><b>Filter:</b>&nbsp;<a href="?" class="' + ((filter == '') ? 'flnk-s' : 'flnk') + '">All</a>' + filter_list;
    document.getElementById('objects').value = 'Objects (' + selected_objects + ')';
}

function getTopLeft(world, pos, otype, id) {
    // in case there is an object without coordinates
    if (pos == '[]') {
        left_pos += 170;
        return 'top:55px; left:' + left_pos + 'px;border:1px solid #000000;width:160px;background-color:#ffffff;color:#000000;';
    }
    try {
        eval('posArray = ' + pos + ';');
        x = posArray[1][0];
        y = posArray[1][1];
    } catch (err) {
        alert('Error: Position format for "' + otype + '", id "' + id + '" is not a valid array!');
        left_pos += 170;
        return 'top:55px; left:' + left_pos + 'px; border:1px solid #000000; width:160px; background-color:#ffffff; color:#000000;';
    }
    if (world == 'Chernarus') {
        x = parseInt(Math.round(x / 10));
        y = parseInt(Math.round(y / 10));
        //catch out of bounds
        if ((x + 100) < 0) {
            left_pos += 170;
            return 'top:55px; left:' + left_pos + 'px; width:160px;';
        } else {
            return 'top:' + (1632 - y) + 'px; left:' + (x + 100) + 'px;';
        }
    }
}

function thisHide(obj, delay) {
    obj.style.visibility = "hidden";
    setTimeout('document.getElementById("' + obj.id + '").style.visibility = "visible";', delay * 1000);
}

function readyToUpdateIn(sec) {
    if (sec < 0) {
        document.getElementById('refresh').value = '';
    } else {
        document.getElementById('refresh').value = 'Ready in ' + sec;
        sec--;
        setTimeout('readyToUpdateIn(' + sec + ');', 1000);
    }
}

function getDamage(parts, name, id) {
    damagedParts = '<br><b>Broken Parts:</b>';
    
    if (parts == '[]') {
        return '';
    }
    else {
        try {
            eval('partsArray = ' + parts + ';');
        }
        catch (err) {
            alert('Error: Parts format for "' + name + '", id "' + id + '" is not a valid array!');
            return '';
        }
        
        for (p = 0; p < partsArray.length; p++) {

        	if (partsArray[p][1] >= 1.0) {
        		damagedParts += '<div style="text-decoration:line-through;">' + partsArray[p][0] + ' (' + (Math.round((1 - partsArray[p][1]) * 100)) + '%)</div>';
        	}
        	else {
        		damagedParts += '<div style="text-decoration:none;">' + partsArray[p][0] + ' (' + (Math.round((1 - partsArray[p][1]) * 100)) + '%)</div>';
        	}     	
        }
        
        for (partNameCzech in partNameEnglish) {
            re = new RegExp(partNameCzech, 'ig');
            damagedParts = damagedParts.replace(re, partNameEnglish[partNameCzech]);
        }
        
        return damagedParts;
    }
}

function getStuff(inv, back, watch_list, name, id) {
    no_backpack = 0;
    try {
        eval('inv_array = ' + inv + ';');
    } catch (err) {
        alert('Error: Inventory format for "' + name + '", id "' + id + '" is not a valid array!');
        inv_array = [];
    }
    try {
        eval('back_array = ' + back + ';');
    } catch (err) {
        alert('Error: Backpack format for "' + name + '", id "' + id + '" is not a valid array!');
        back_array = [];
    }
    all_stuff = new Array();
    // inventory format
    if (typeof (inv_array[0]) != 'undefined' && inv_array[0] != '') {
        for (s = 0; s < inv_array[0].length; s++) {
            qty = all_stuff[inv_array[0][s]];
            all_stuff[inv_array[0][s]] = (typeof (qty) == 'undefined') ? 1 : (qty + 1);
        }
    }
    if (typeof (inv_array[1]) != 'undefined' && inv_array[1] != '') {
        for (s = 0; s < inv_array[1].length; s++) {
            qty = all_stuff[inv_array[1][s]];
            all_stuff[inv_array[1][s]] = (typeof (qty) == 'undefined') ? 1 : (qty + 1);
        }
    }
    // backpack format
    if (typeof (back_array[0]) != 'undefined' && back_array[0] != '') {
        qty = all_stuff[back_array[0]];
        all_stuff[back_array[0]] = (typeof (qty) == 'undefined') ? 1 : (qty + 1);
    } else {
        no_backpack = 1;
    }
    if (typeof (back_array[1]) != 'undefined' && typeof (back_array[1][0]) != 'undefined' && typeof (back_array[1][1]) != 'undefined') {
        for (s = 0; s < back_array[1][0].length; s++) {
            qty = all_stuff[back_array[1][0][s]];
            all_stuff[back_array[1][0][s]] = (typeof (qty) == 'undefined') ? back_array[1][1][s] : qty + back_array[1][1][s];
        }
    }
    if (typeof (back_array[2]) != 'undefined' && typeof (back_array[2][0]) != 'undefined' && typeof (back_array[2][1]) != 'undefined') {
        for (s = 0; s < back_array[2][0].length; s++) {
            qty = all_stuff[back_array[2][0][s]];
            all_stuff[back_array[2][0][s]] = (typeof (qty) == 'undefined') ? back_array[2][1][s] : qty + back_array[2][1][s];
        }
    }
    inv_all = new Array();
    for (n in all_stuff) {
        inv_all.push(all_stuff[n] + ' x ' + n);
    }
    if (inv_all.length > 0) {
        stuff_str = '<br><br>' + inv_all.join('<br>');
        for (f = 0; f < watch_list.length; f++) {
            re = new RegExp(watch_list[f], 'ig');
            stuff_str = stuff_str.replace(re, '<span class="flag">$1(!)</span>');
        }
        return stuff_str.replace(/Item/g, '').replace(/Food/g, '').replace(/Soda/g, '') + ((no_backpack) ? '<br>NO_BACKPACK' : '');
    } else {
        return '';
    }
}

function getStuff2(inv, watch_list, name, id) {
    try {
        eval('inv_array = ' + inv + ';');
    } catch (err) {
        alert('Error: Inventory format for "' + name + '", id "' + id + '" is not a valid array!');
        inv_array = [];
    }
    all_stuff = new Array();
    if (typeof (inv_array[0]) != 'undefined' && typeof (inv_array[0][0]) != 'undefined' && typeof (inv_array[0][1]) != 'undefined') {
        for (s = 0; s < inv_array[0][0].length; s++) {
            qty = all_stuff[inv_array[0][0][s]];
            all_stuff[inv_array[0][0][s]] = (typeof (qty) == 'undefined') ? inv_array[0][1][s] : qty + inv_array[0][1][s];
        }
    }
    if (typeof (inv_array[1]) != 'undefined' && typeof (inv_array[1][0]) != 'undefined' && typeof (inv_array[1][1]) != 'undefined') {
        for (s = 0; s < inv_array[1][0].length; s++) {
            qty = all_stuff[inv_array[1][0][s]];
            all_stuff[inv_array[1][0][s]] = (typeof (qty) == 'undefined') ? inv_array[1][1][s] : qty + inv_array[1][1][s];
        }
    }
    if (typeof (inv_array[2]) != 'undefined' && typeof (inv_array[2][0]) != 'undefined' && typeof (inv_array[2][1]) != 'undefined') {
        for (s = 0; s < inv_array[2][0].length; s++) {
            qty = all_stuff[inv_array[2][0][s]];
            all_stuff[inv_array[2][0][s]] = (typeof (qty) == 'undefined') ? inv_array[2][1][s] : qty + inv_array[2][1][s];
        }
    }
    inv_all = new Array();
    for (n in all_stuff) {
        inv_all.push(all_stuff[n] + ' x ' + n);
    }
    if (inv_all.length > 0) {
        stuff_str = '<br><br>' + inv_all.join('<br>');
        for (f = 0; f < watch_list.length; f++) {
            re = new RegExp(watch_list[f], 'ig');
            stuff_str = stuff_str.replace(re, '<span class="flag">$1(!)</span>');
        }
        return stuff_str.replace(/Item/g, '').replace(/Food/g, '').replace(/Soda/g, '');
    } else {
        return '';
    }
    return (inv_all.length > 0) ? '<br><br>' + inv_all.join('<br>').replace(/Item/g, '') : '';
}

function interpretState(db_state, name, id) {
    tr = new Array();
    // where
    tr['Aldr'] = '<br>- on ladder';
    tr['Aswm'] = '<br>- in water';
    tr['Ainv'] = '<br>- viewing inventory';
    tr['Adth'] = '<br>- slowly dying';
    // pose
    tr['Perc'] = '<br>- standing up';
    tr['Pknl'] = '<br>- crouching down';
    tr['Ppne'] = '<br>- prone';
    tr['Psit'] = '<br>- sitting down';
    tr['Pfal'] = '<br>- falling down';
    tr['Psqt'] = '<br>- squatting';
    // moving
    tr['Mstp'] = '<br>- not moving';
    tr['Mwlk'] = '<br>- slowly moving';
    tr['Mrun'] = '<br>- moving';
    tr['Mspr'] = '<br>- fast moving';
    tr['Mlen'] = '<br>- leaning behind corner';
    tr['Mtrn'] = '<br>- turning';
    tr['Mjmp'] = '<br>- jumping';
    tr['Mwtl'] = '<br>- swiming';
    tr['Muwt'] = '<br>- diving under water';
    tr['Meva'] = '<br>- making evasive moves';
    tr['Mmnt'] = '<br>- getting on vehicle';
    tr['Mdnt'] = '<br>- getting off vehicle';
    // stance
    tr['Srld'] = '<br>- reloading weapon';
    tr['Sgth'] = '<br>- throwing grenade';
    tr['Sgrl'] = '<br>- rolling grenade';
    // weapon holding
    tr['Wdog'] = '<br>- holding a dog';
    tr['Wcwo'] = '<br>- carrying a wounded person';
    tr['Wbin'] = '<br>- using binoculars';
    // moving
    tr['Df'] = ' forward';
    tr['Dfl'] = ' left-forward';
    tr['Dl'] = ' to the left';
    tr['Dbl'] = ' left-backwards';
    tr['Dbr'] = ' right-backwards';
    tr['Dr'] = ' to the right';
    tr['Dfr'] = ' right-forward';
    tr['Dup'] = ' upwards';
    tr['Ddn'] = ' downwards';
    try {
        eval('state_array = ' + db_state + ';');
    } catch (err) {
        alert('Error: State format for "' + name + '", id "' + id + '" is not a valid array!');
        return '<br>STATE_UNKNOWN';
    }
    weapon_name = (state_array[0] != '') ? '<br>- holding ' + state_array[0] : '';
    if (state_array[1] != '') {
        animation_str = state_array[1].replace(/_.*$/, '').replace(/^a(\w{3})p(\w{3})m(\w{3})s(\w{3})w(\w{3})d(\w+)$/, 'A$1|P$2|M$3|S$4|W$5|D$6');
        if (animation_str != '') {
            animation = animation_str.split(/\|/);
            if (animation.length == 6) {
                return (tr[animation[1]] + tr[animation[2]] + tr[animation[5]] + weapon_name + tr[animation[4]] + tr[animation[0]] + tr[animation[3]]).replace(/undefined/g, '');
            } else {
                return '<br>IN_VEHICLE<br>' + state_array[1];
            }
        }
    }
    return '<br>STATE_UNKNOWN';
}