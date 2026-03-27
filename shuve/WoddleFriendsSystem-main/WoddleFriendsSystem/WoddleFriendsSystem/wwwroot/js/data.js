var NumOfUsers = 5;

var user0 = {
    name: ["Alice", "A for Acrocanthosaurus"],
    friends: ["Bob", "Clare", "Esther"],
    friendrequests: ["Daniel"],
    closefriends: ["Clare"],
    users: []
};

localStorage.setItem("user0", JSON.stringify(user0));


var user1 = {
    name: ["Bob", "Running from the law"],
    friends: ["Alice", "Esther"],
    friendrequests: ["Clare"],
    closefriends: ["Alice"],
    users: ["Daniel"]
};

localStorage.setItem("user1", JSON.stringify(user1));

var user2 = {
    name: ["Clare", "City gal"],
    friends: ["Alice", "Esther"],
    friendrequests: ["Daniel"],
    closefriends: ["Alice"],
    users: ["Bob"]
};

localStorage.setItem("user2", JSON.stringify(user2));

var user3 = {
    name: ["Daniel", "Sup"],
    friends: [],
    friendrequests: ["Alice"],
    closefriends: [],
    users: ["Bob", "Clare", "Esther"]
};

localStorage.setItem("user3", JSON.stringify(user3));

var user4 = {
    name: ["Esther", "Cheese pizzas"],
    friends: ["Alice", "Clare", "Bob"],
    friendrequests: ["Daniel"],
    closefriends: ["Clare"],
    users: ["Alice"]
};

localStorage.setItem("user4", JSON.stringify(user4));