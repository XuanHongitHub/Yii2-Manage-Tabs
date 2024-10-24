
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


// function addColumn() {
//     const columnContainer = document.getElementById('columnsContainer');
//     const newColumnDiv = document.createElement('div');
//     newColumnDiv.classList.add('input-group', 'mb-2');
//     newColumnDiv.innerHTML = `
//         <input type="text" class="form-control" placeholder="Enter column name" required>
//         <button class="btn btn-outline-secondary" type="button" onclick="removeColumn(this)">Remove</button>
//     `;
//     columnContainer.appendChild(newColumnDiv);
// }

// function removeColumn(button) {
//     button.parentElement.remove(); // Xóa cột
// }




// // Xử lý thêm bảng
// document.addEventListener('DOMContentLoaded', function () {
//     document.getElementById('saveTableBtn').addEventListener('click', function (event) {
//         event.preventDefault();
//         console.log('Submit button clicked');

//         const tableName = document.getElementById('tableName').value;
//         const columns = Array.from(document.querySelectorAll('#columnsContainer input')).map(input => input.value).join(', ');

//         const formData = new FormData();
//         formData.append('title', tableName);
//         formData.append('tab_type', 'table');
//         formData.append('columns', columns);

//         fetch('/tabs/add-table', {
//             method: 'POST',
//             body: formData,
//             headers: {
//                 'X-CSRF-Token': csrfToken
//             }
//         })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) {
//                     alert(data.message);
//                     location.reload();
//                 } else {
//                     alert(data.message);
//                 }
//             })
//             .catch(error => console.error('Error:', error));
//     });
// });

// // Xử lý thêm richtext
// document.getElementById('saveRichtextButton').addEventListener('click', function () {
//     const richtextTitle = document.getElementById('richtextTitle').value;
//     const richtextContent = document.querySelector('.richtext-area').innerHTML;

//     const formData = new FormData();
//     formData.append('title', richtextTitle);
//     formData.append('content', richtextContent);

//     fetch('/tabs/add-richtext', {
//         method: 'POST',
//         body: formData,
//         headers: {
//             'X-CSRF-Token': csrfToken
//         }
//     })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 alert(data.message);
//                 location.reload();
//             } else {
//                 alert(data.message);
//             }
//         })
//         .catch(error => console.error('Error:', error));
// });