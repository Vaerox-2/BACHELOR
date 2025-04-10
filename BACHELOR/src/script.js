



let dice = [];
let numDice = 3;
let check = 0;
let currentPoints = 0;

resetGame();


function resetGame() {
    resetDice();
    updateScoreLine();
}
function resetDice() {
    dice = [];
    for (let i = 0; i < numDice; i++) {
        dice[i] = Math.floor(Math.random()*6+1);
        updateDice(i);
    }
    updateScoreLine();
}

function rollDices() {
    let die = document.getElementById("roleDiceButton")
    die.removeAttribute("onclick");
    die.style.backgroundColor = "gray";

    if (Number(currentPoints) < Number(document.getElementById("betInput").value) ) {
        alert("Not enough balance to roll");
        document.getElementById("roleDiceButton").style.backgroundColor = "white";
        document.getElementById("roleDiceButton").setAttribute("onclick", "rollDices()");
    } else {
        for (let i = 0; i < dice.length; i++) {
            dice[i] = Math.floor(Math.random()*6+1);
            updateDice(i);
        }
        changeScore();
    }
}

function updateScoreLine() {
    let scoreLine = document.getElementById("scoreLine");
    scoreLine.innerText = "Awaiting database";

    $.ajax({
        url:'updateScore.php',
        method:'POST',
        data:{
        },
        success:function(response){
            currentPoints = response;
            scoreLine.innerHTML = "Balance: " + response;
            document.getElementById("roleDiceButton").style.backgroundColor = "white";
            document.getElementById("roleDiceButton").setAttribute("onclick", "rollDices()");
        }
    });
}

function changeScore(){
    let bet = document.getElementById("betInput").value;
    let mult = calculateMultiplier(dice);
    let total = bet * mult;
    $.ajax({
        url:'updateScore.php',
        method:'POST',
        data:{
            points:total
        },
        success:function(response){
            updateScoreLine();
        }
    });
}

function calculateMultiplier(dice) {
    let count = 0;
    for (let i = 0; i < dice.length; i++) {
        if (dice[i] == 6) {
            count += 1;
        }
    }
    if (count === 3) {
        return 2;
    } else {
        return -1;
    }
}

function updateDice(i) {
    let die = document.getElementById("dice"+(i+1));
    die.src = "img/" + dice[i] + ".png";
}

