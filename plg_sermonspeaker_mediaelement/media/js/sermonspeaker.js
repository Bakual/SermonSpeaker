document.addEventListener("DOMContentLoaded", function () {
    ss_eventListeners();
});

function ss_eventListeners() {
    let elements = document.getElementsByClassName("ss-play");
    Array.from(elements).forEach(function (element) {
        element.addEventListener('click', ss_playListItem);
    });
    document.getElementById("mediaspace1").addEventListener('play', ss_changeListItem);
}

function ss_play() {
}

function ss_playListItem() {
    let id = this.dataset.id;
    let playerid = this.dataset.player;
    let player = document.getElementById(playerid);
    let activeplayer = player.lastChild.player;
    activeplayer.currentPlaylistItem = id;
    player.setSrc(activeplayer.playlist[id].src);
    player.load();
    player.play();
}

function ss_changeListItem(event) {
    let activeplayer = document.getElementById('mediaspace1').lastChild.player;
    let entry = activeplayer.playlist[activeplayer.currentPlaylistItem];
    for (var i = 0; document.getElementById("sermon" + i); i++) {
        document.getElementById("sermon" + i).classList.remove("ss-current");
    }
    document.getElementById("sermon" + activeplayer.currentPlaylistItem).classList.add("ss-current");
    if (entry['duration']) {
        document.getElementById('playing-duration').innerHTML = entry['duration'];
    } else {
        document.getElementById('playing-duration').innerHTML = '';
    }
    document.getElementById("playing-pic").src = entry['data-thumbnail'];
    if (entry['data-thumbnail']) {
        document.getElementById('playing-pic').style.display = 'block';
    } else {
        document.getElementById('playing-pic').style.display = 'none';
    }
    if (entry.error) {
        document.getElementById('playing-error').innerHTML = entry.error;
        document.getElementById('playing-error').style.display = 'block';
    } else {
        document.getElementById('playing-error').style.display = 'none';
    }
    document.getElementById('playing-title').innerHTML = entry.title;
    document.getElementById('playing-desc').innerHTML = entry.description;
}