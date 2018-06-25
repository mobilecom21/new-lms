(function () {
    var randomString;
    randomString = Math.random()
        .toString(36)
        .substring(7);

    var _forEach;
    _forEach = function (_a, _cb) {
        for (var i = 0; i < _a.length; i++) {
            _cb(_a[i], _a, i);
        }
    };

    var onSubmit = function (event) {
        event.preventDefault();
        event.stopPropagation();

        var submittingElement = document.getElementsByClassName('magic-submitting');
        if (submittingElement.length > 0) {
            return false;
        }

        // clear errors
        _forEach(this.querySelectorAll('.form-group'), function (e) {
            e.classList.remove('error');
        });

        _forEach(this.querySelectorAll('[type="submit"]'), function (e) {
            e.classList.add('magic-submitting');
        });

        _forEach(this.querySelectorAll('[data-random-' + randomString + ']'), function (e) {
            e.parentNode.removeChild(e);
        });

        var request;
        request = new XMLHttpRequest();
        request.open("POST", this.getAttribute('action'), true);
        request.send(new FormData(this));
        request.onreadystatechange = function () {

            _forEach(document.querySelectorAll('[type="submit"]'), function (e) {
                e.classList.remove('magic-submitting');
            });

            if (request.readyState !== 4) {
                return false;
            }

            var res;
            try {
                res = JSON.parse(request.response || '');
            }
            catch (e) {
                res = {
                    redirectTo: "",
                    errors: {}
                };
            }

            if (res.redirectTo) {
                window.location = res.redirectTo;
                return false;
            }

            if (res.redirectTo) {
                window.location = res.redirectTo;
                return false;
            }

            if (res.successMessage) {
                var successElements = document.getElementsByClassName('success-message');
                while (successElements.length > 0) successElements[0].remove();
                var successElement;
                successElement = document.createElement('div');
                successElement.classList.add('success-message');
                successElement.innerHTML = '<div class="alert alert-success alert-no-border alert-close alert-dismissible fade show" role="alert"> ' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button> '
                    + res.successMessage +' </div>';
                event.target.appendChild(successElement);
                return false;
            }

            if (res.errorMessage) {
                var errorElements = document.getElementsByClassName('error-message');
                while (errorElements.length > 0) errorElements[0].remove();
                var errorElement;
                errorElement = document.createElement('div');
                errorElement.classList.add('error-message');
                errorElement.innerHTML = '<div class="alert alert-danger alert-no-border alert-close alert-dismissible fade show" role="alert"> ' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button> '
                    + res.errorMessage +' </div>';
                event.target.appendChild(errorElement);
            }


            _forEach(Object.keys(res.errors), function (errorField) {
                var currentElement, parentNode;
                currentElement = event.target.querySelector('[name=' + errorField + ']');
                parentNode = currentElement.parentNode;
                parentNode.classList.add('error');

                Object.keys(res.errors[errorField]).forEach(function (errorType) {
                    var helpBlock;
                    helpBlock = document.createElement('span');
                    helpBlock.setAttribute('data-random-' + randomString, '');
                    helpBlock.classList.add('help-block');
                    helpBlock.innerText = res.errors[errorField][errorType];
                    parentNode.appendChild(helpBlock);
                });
            });
        };
    };

    _forEach(document.querySelectorAll('form[method=POST]'), function (e) {
        e.addEventListener('submit', onSubmit);
    });
})();