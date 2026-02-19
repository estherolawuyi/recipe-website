console.log("eventHandlers.js is running!");

function validateName(name) {
	let nameRegEx = /^[a-zA-Z0-9_]+$/;
	if (nameRegEx.test(name))
		return true;
	else
		return false;
}

function validateEmail(email) {
let emailRegEx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	if (emailRegEx.test(email))
		return true;
	else
		return false;
}

function validatePassword(password) {
	let length = password.length >= 6;
	let space = !/\s/.test(password);
	let letter = /[^a-zA-Z]/.test(password);

	if (length && space && letter)
		return true;
	else
		return false;
}

function validateConfirmPassword(cpassword, password) {
    if (cpassword == password)
		return true;
	else
		return false;
}

function validateDOB(dob) {
	let dobRegEx = /^\d{4}[-]\d{2}[-]\d{2}$/;
	if (dobRegEx.test(dob))
		return true;
	else
		return false;
}

function validateAvatar(avatar) {
	let avatarRegEx = /^[^\n]+\.[a-zA-Z]{3,4}$/;
	if (avatarRegEx.test(avatar))
	    return true;
	else
	    return false;
}

function fNameHandler(event) {
	let fname = event.target;
	if (validateName(fname.value)) {
		return true;
	} else {
		return false;
	}
}

function lNameHandler(event) {
	let lname = event.target;
	if (validateName(lname.value)) {
		return true;
	} else {
		return false;
	}
}

function emailHandler(event) {
	let email = event.target;
	if (validateEmail(email.value)) {
		return true;
	} else {
		return false;
	}
}

function pwdHandler(event) {
	let pwd = event.target;
	if (validatePassword(pwd.value)) {
		return true;
	} else {
		return false;
	}
}

function cpwdHandler(event) {
	let pwd = document.getElementById("password");
	let cpwd = event.target;
	if (pwd.value === cpwd.value) {
		return true;
	} else {
		return false;
	}
}

function dobHandler(event) {
	let dob = event.target;
	if (validateDOB(dob.value)) {
		return true;
	} else {
		return false;
	}
}

function avatarHandler(event) {
	let avatar = event.target;
	if (validateAvatar(avatar.value)) {
		return true;
	} else {
		return false;
	}
}


function validateLogin(event) {

	let email = document.getElementById("email");
	let pwd = document.getElementById("password");

	let formIsValid = true;

	if (validateEmail(email.value)) {
        console.log("\t- email is: " + email.value);
		document.getElementById("error-text-email").classList.add("hidden");
    } else {
        console.log("\t- email is NOT VALID!");
		document.getElementById("error-text-email").classList.remove("hidden");
        formIsValid = false;
    }

	if (validatePassword(pwd.value)) {
        console.log("\t- password is: " + pwd.value);
		document.getElementById("error-text-password").classList.add("hidden");
    } else {
        console.log("\t- password is NOT VALID!");
		document.getElementById("error-text-password").classList.remove("hidden");
        formIsValid = false;
    }
	
	if (formIsValid === false) {
		event.preventDefault();
	}
	else {
		console.log("validation successful, sending data to the server");
	}
}

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("login").addEventListener("submit", validateLogin);

	let email = document.getElementById("email");
    if (email) {
        email.addEventListener("blur", emailHandler);
    }

    let pwd = document.getElementById("password");
    if (pwd) {
        pwd.addEventListener("blur", pwdHandler);
    }
});

function validateSignup(event) {
	let fname = document.getElementById("fname");
	let lname = document.getElementById("lname");
	let email = document.getElementById("email");
    let pwd = document.getElementById("password");
	let cpwd = document.getElementById("cpassword");
	let dob = document.getElementById("dob");
	let avatar = document.getElementById("profilephoto");

	let formIsValid = true;
    
	if (validateName(fname.value)) {
        console.log("\t- first name is: " + fname.value);
		document.getElementById("error-text-fname").classList.add("hidden");
    } else {
        console.log("\t- first name is NOT VALID!");
		document.getElementById("error-text-fname").classList.remove("hidden");
        formIsValid = false;
    }

	if (validateEmail(email.value)) {
        console.log("\t- email is: " + email.value);
		document.getElementById("error-text-email").classList.add("hidden");
    } else {
        console.log("\t- email is NOT VALID!");
		document.getElementById("error-text-email").classList.remove("hidden");
        formIsValid = false;
    }

    if (validateName(lname.value)) {
        console.log("\t- last name is: " + lname.value);
		document.getElementById("error-text-lname").classList.add("hidden");
    } else {
        console.log("\t- last name is NOT VALID!");
		document.getElementById("error-text-lname").classList.remove("hidden");
        formIsValid = false;
    }

	if (validatePassword(pwd.value)) {
        console.log("\t- password is: " + pwd.value);
		document.getElementById("error-text-password").classList.add("hidden");
    } else {
        console.log("\t- password is NOT VALID!");
		document.getElementById("error-text-password").classList.remove("hidden");
        formIsValid = false;
    }

	if (validateConfirmPassword(cpwd.value, pwd.value)) {
        console.log("\t- password is: " + pwd.value);
		document.getElementById("error-text-cpassword").classList.add("hidden");
    } else {
        console.log("\t- passwords are not the same");
		document.getElementById("error-text-cpassword").classList.remove("hidden");
        formIsValid = false;
    }

	if (validateDOB(dob.value)) {
        console.log("\t- dob is: " + dob.value);
		document.getElementById("error-text-dob").classList.add("hidden");
    } else {
        console.log("\t- dob is NOT VALID!");
		document.getElementById("error-text-dob").classList.remove("hidden");
        formIsValid = false;
    }
	
	if (validateAvatar(avatar.value)) {
        console.log("\t- avatar is: " + avatar.value);
		document.getElementById("error-text-profilephoto").classList.add("hidden");
    } else {
        console.log("\t- avatar is NOT VALID!");
		document.getElementById("error-text-profilephoto").classList.remove("hidden");
        formIsValid = false;
    }
	
	if (formIsValid === false) {
	    event.preventDefault();
	}
	else {
		console.log("validation successful, sending data to the server");
	}
}

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("signup").addEventListener("submit", validateSignup);

	let fname = document.getElementById("fname")
	if (fname) {
        fname.addEventListener("blur", fnameHandler);
    }

	let lname = document.getElementById("lname")
	if (lname) {
        lname.addEventListener("blur", lnameHandler);
	}

	let email = document.getElementById("email");
    if (email) {
        email.addEventListener("blur", emailHandler);
    }

    let pwd = document.getElementById("password");
    if (pwd) {
        pwd.addEventListener("blur", pwdHandler);
    }

	let cpwd = document.getElementById("cpassword");
	if (cpwd) {
        cpwd.addEventListener("blur", cpwdHandler);
    }

	let dob = document.getElementById("dob");
    if (dob) {
        dob.addEventListener("blur", dobHandler);
	}

	let avatar = document.getElementById("profilephoto");
    if (avatar) {
        avatar.addEventListener("blur", avatarHandler);
	}
});


function charCounter() {
	const form = document.getElementById("noteForm")
	const MAX = 1300;
	const text = document.getElementById("noteText");
	const counter = document.getElementById("noteCounter");
	const charCount = document.getElementById("charCount");
	const errNote = document.getElementById("err-note");	
	const WARNING = MAX * 0.75;

	if (!form || !text || !counter || !charCount || !errNote) {
    	return;
    }
	
	function updateCounter() {
		const curr = text.value.length;
		document.getElementById("charCount").textContent = curr;

		const charOver = counter.querySelector("#charOver");
		counter.classList.remove("near-limit", "over-limit");
		charOver.textContent = '';

		if (curr > MAX) {
			counter.classList.add("over-limit");
			charOver.textContent = ` (${curr - MAX} over limit)`;
		} else if (curr > WARNING) {
			counter.classList.add("near-limit");
		}
	}

	text.addEventListener('input', function (event) {
		updateCounter();
		if (errNote.textContent) {
			errNote.textContent = "";
			errNote.classList.remove("error-visible");
	    }
    });
	
	if (form && text) {
		form.addEventListener("submit", function (event) {
			const note = text.value.trim();

			if (note === "") {
				errNote.textContent = "Note cannot be blank.";
     		    errNote.classList.add("error-visible");
				event.preventDefault();
				console.log("Note is empty.");
			} else if (note.length > MAX) {
				errNote.textContent = "Note too long.";
                errNote.classList.add("error-visible");
				event.preventDefault();
				console.log(`Note is too long (${note.length}/1300)`);
			} else {
				errNote.textContent = "";
                errNote.classList.remove("error-visible");
				console.log("Note submitted:");
			}
		});
	}
}

document.addEventListener("DOMContentLoaded", charCounter);

document.addEventListener('DOMContentLoaded', function() {
        const createRecipeForm = document.getElementById('create-recipe-form');
        const recipeTitleInput = document.getElementById('recipe-title');
        const recipeIngredientsTextarea = document.getElementById('recipe-ingredients');
        const recipeStepsTextarea = document.getElementById('recipe-steps');

        const recipeTitleError = document.getElementById('err-title');
        const recipeIngredientsError = document.getElementById('err-ingredients');
        const recipeStepsError = document.getElementById('err-steps');

        const MAX_TITLE_LENGTH = 256;

		if (!createRecipeForm) {
            return;
    	}


        function validateTextField(inputElement, errorElement, fieldName, maxLength = Infinity) {
			const value = inputElement.value.trim();
			if (fieldName === "Recipe Title") {
				if (!value.trim()) {
					errorElement.textContent = "Recipe title cannot be blank.";
					console.log('field cannot be blank.');
					return false;
				}
				if (value.length > maxLength) {
					console.log('field must be no more than 256 characters.');
					errorElement.textContent = "Recipe title cannot exceed 256 characters";
					return false;
				}
					errorElement.textContent = "";
					return true;
			}
		}
       
			createRecipeForm.addEventListener('submit', function(event) {
			event.preventDefault(); 

			const isRecipeTitleValid = validateTextField(recipeTitleInput, recipeTitleError, 'Recipe Title', MAX_TITLE_LENGTH);

			if (isRecipeTitleValid) {
				console.log('Form is valid. Submitting recipe creation.');
				alert('Recipe created successfully (simulated)!');
			createRecipeForm.reset(); 
			} else {
				console.log('Form cannot be submitted');
			}
    });
});

function checkNewRecipes() {
    const container = document.getElementById('recipe-list');
    if (!container) return;

    let latestRecipe = container.querySelector('.recipe');
    let latestCreated = latestRecipe ? latestRecipe.dataset.created : '1970-01-01 00:00:00';

    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'ajax_newrecipes.php?latest=' + encodeURIComponent(latestCreated), true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                const newRecipes = JSON.parse(xhr.responseText);
                if (Array.isArray(newRecipes) && newRecipes.length > 0) {
                    insertNewRecipes(newRecipes, container);
                }
            } catch (e) {
                console.error('Invalid JSON from ajax_newrecipes.php', e);
            }
        }
    };
    xhr.send();
}

function insertNewRecipes(recipes, container) {
    recipes.forEach(recipe => {
        const div = document.createElement('div');
        div.className = 'recipe';
        div.dataset.created = recipe.created_at; 

        div.innerHTML = `
            <h3>Title: ${recipe.title}</h3>
            <h4>Creator: @${recipe.creator}</h4>
            <h4>Created on: ${recipe.created_at}</h4>
            <h4>Note Count: ${recipe.note_count}</h4>
            <div class="recipe-options">
                <a class="button" href="viewrecipe.php?id=${recipe.recipe_id}">View Recipe</a>
                <a class="button" href="manageaccess.php?id=${recipe.recipe_id}">Manage Access</a>
            </div>
        `;

        container.insertBefore(div, container.firstChild);
    });
}

setInterval(checkNewRecipes, 90000);

document.addEventListener('DOMContentLoaded', checkNewRecipes);

document.addEventListener('DOMContentLoaded', function() {
    const noteForm = document.getElementById('noteForm');
    const noteText = document.getElementById('noteText');
    const noteList = document.getElementById('note-list');
    const errNote = document.getElementById('err-note');
    const recipeId = <?= $recipe_id ?>;

    noteForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const text = noteText.value.trim();

        if (!text || text.length > 1300) {
            errNote.textContent = !text ? "Note cannot be blank." : "Note too long.";
            errNote.classList.add("error-visible");
            return;
        }

        fetch('ajax_note.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `recipe_id=${recipeId}&noteText=${encodeURIComponent(text)}`
        })
        .then(res => res.json())
        .then(newNotes => {
            newNotes.forEach(note => {
                const div = document.createElement('div');
                div.className = 'note';
                div.innerHTML = `
                    <img src="${note.avatar_url || 'images/default-avatar.png'}" class="avatar" />
                    <div class="note-content">
                        <strong>@${note.email}</strong>
                        <p class="timestamp">${note.timestamp}</p>
                        <p>${note.note}</p>
                    </div>
                `;
                noteList.appendChild(div);
            });
            noteText.value = '';
            errNote.textContent = '';
        })
        .catch(console.error);
    });
});