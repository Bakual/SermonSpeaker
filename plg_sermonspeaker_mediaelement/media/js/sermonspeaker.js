document.addEventListener("DOMContentLoaded", function () {
    ss_eventListeners();
});

function ss_eventListeners() {
    let elements = document.getElementsByClassName("ss-play");
    Array.from(elements).forEach(function (element) {
        element.addEventListener('click', ss_playListItem);
    });
    document.getElementById("mediaspace1").addEventListener('play', ss_changeListItem);
    let toggleplayer = document.getElementById("mediaspace1-other");
    if (toggleplayer) {
        toggleplayer.addEventListener('play', ss_changeListItem);
    }
}

function ss_play() {
}

function ss_playListItem() {
    let id = this.dataset.id;
    let playerid = this.dataset.player;
    let player = document.getElementById(playerid);
    if (player.lastElementChild.classList.contains('hidden')) {
        player = document.getElementById(playerid + '-other');
    }
    let activeplayer = player.lastChild.player;
    activeplayer.currentPlaylistItem = id;
    player.setSrc(activeplayer.playlist[id].src);
    player.load();
    player.play();
}

function ss_changeListItem(event) {
    let activeplayer;
    if (document.getElementById('mediaspace1').lastElementChild.classList.contains('hidden')) {
        activeplayer = document.getElementById('mediaspace1-other').lastChild.player;
    } else {
        activeplayer = document.getElementById('mediaspace1').lastChild.player;
    }
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

function Video() {
    let player1 = document.getElementById('mediaspace1').lastChild;
    let player2 = document.getElementById('mediaspace1-other').lastChild;
    if (player1.nodeName == 'AUDIO') {
        player1.pause();
        player1.classList.add('hidden');
        player1.parentNode.parentNode.parentNode.parentNode.classList.add('hidden');
        player2.classList.remove('hidden');
        player2.parentNode.parentNode.parentNode.parentNode.classList.remove('hidden');
    } else {
        player1.classList.remove('hidden');
        player1.parentNode.parentNode.parentNode.parentNode.classList.remove('hidden');
        player2.pause();
        player2.classList.add('hidden');
        player2.parentNode.parentNode.parentNode.parentNode.classList.add('hidden');
    }
 }
function Audio() {
    let player1 = document.getElementById('mediaspace1').firstElementChild;
    let player2 = document.getElementById('mediaspace1-other').firstElementChild;
    if (player1.nodeName == 'VIDEO') {
        player1.pause();
        player1.classList.add('hidden');
        player1.parentNode.parentNode.parentNode.parentNode.classList.add('hidden');
        player2.classList.remove('hidden');
        player2.parentNode.parentNode.parentNode.parentNode.classList.remove('hidden');
    } else {
        player1.classList.remove('hidden');
        player1.parentNode.parentNode.parentNode.parentNode.classList.remove('hidden');
        player2.pause();
        player2.classList.add('hidden');
        player2.parentNode.parentNode.parentNode.parentNode.classList.add('hidden');
    }
}