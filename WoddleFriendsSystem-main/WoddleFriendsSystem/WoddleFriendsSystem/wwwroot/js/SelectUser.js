
function user0select() {
    var userNameSelect = "user0"
    localStorage.setItem("userNameSelect", JSON.stringify(userNameSelect));

    var retrieveNameSelect = JSON.parse(localStorage.getItem("userNameSelect"));
    var retrieveName = (JSON.parse(localStorage.getItem(retrieveNameSelect)));
    localStorage.setItem("name", JSON.stringify(retrieveName.name[0]));

    document.getElementById('nameSelected').textContent = JSON.parse(localStorage.getItem("name"));

}
function user1select() {
    var userNameSelect = "user1"
    localStorage.setItem("userNameSelect", JSON.stringify(userNameSelect));

    var retrieveNameSelect = JSON.parse(localStorage.getItem("userNameSelect"));
    var retrieveName = (JSON.parse(localStorage.getItem(retrieveNameSelect)));
    localStorage.setItem("name", JSON.stringify(retrieveName.name[0]));

    document.getElementById('nameSelected').textContent = JSON.parse(localStorage.getItem("name"));
}
function user2select() {
    var userNameSelect = "user2"
    localStorage.setItem("userNameSelect", JSON.stringify(userNameSelect));

    var retrieveNameSelect = JSON.parse(localStorage.getItem("userNameSelect"));
    var retrieveName = (JSON.parse(localStorage.getItem(retrieveNameSelect)));
    localStorage.setItem("name", JSON.stringify(retrieveName.name[0]));

    document.getElementById('nameSelected').textContent = JSON.parse(localStorage.getItem("name"));
}
function user3select() {
    var userNameSelect = "user3"
    localStorage.setItem("userNameSelect", JSON.stringify(userNameSelect));

    var retrieveNameSelect = JSON.parse(localStorage.getItem("userNameSelect"));
    var retrieveName = (JSON.parse(localStorage.getItem(retrieveNameSelect)));
    localStorage.setItem("name", JSON.stringify(retrieveName.name[0]));

    document.getElementById('nameSelected').textContent = JSON.parse(localStorage.getItem("name"));
}
function user4select() {
    var userNameSelect = "user4"
    localStorage.setItem("userNameSelect", JSON.stringify(userNameSelect));

    var retrieveNameSelect = JSON.parse(localStorage.getItem("userNameSelect"));
    var retrieveName = (JSON.parse(localStorage.getItem(retrieveNameSelect)));
    localStorage.setItem("name", JSON.stringify(retrieveName.name[0]));

    document.getElementById('nameSelected').textContent = JSON.parse(localStorage.getItem("name"));
}


