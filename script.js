document.addEventListener('DOMContentLoaded', function () {
    // Update the hidden amount field when the membership type changes
    const membershipTypeDropdown = document.getElementById('membershipType');
    const amountField = document.getElementById('amount');

    if (membershipTypeDropdown) {
        membershipTypeDropdown.addEventListener('change', function () {
            if (membershipTypeDropdown.value === 'Silver') {
                amountField.value = '50'; // Silver membership amount
            } else if (membershipTypeDropdown.value === 'Gold') {
                amountField.value = '100'; // Gold membership amount
            }
        });
    }
});





    $(document).ready(function () {
        $('#myTable').DataTable(); // Initialize DataTable

        // Edit button handler
        $('.edit').on('click', function () {
            const memberId = $(this).data('id');
            const firstName = $(this).data('firstname');
            const lastName = $(this).data('lastname');
            const contactNumber = $(this).data('contact');
            const email = $(this).data('email');
            const membershipType = $(this).data('membership');
            const classId = $(this).data('class');
            const attendanceDate = $(this).data('attendance');
            const paymentDate = $(this).data('payment');
            const amount = $(this).data('amount');

            // Populate modal fields
            $('#memberIdEdit').val(memberId);
            $('#firstNameEdit').val(firstName);
            $('#lastNameEdit').val(lastName);
            $('#contactNumberEdit').val(contactNumber);
            $('#emailEdit').val(email);
            $('#membershipTypeEdit').val(membershipType);
            $('#classIdEdit').val(classId);
            $('#attendanceDateEdit').val(attendanceDate);
            $('#paymentDateEdit').val(paymentDate);
            $('#amountEdit').val(amount);
        });
    });
  



    
   

    // $(document).ready(function () {
    //     $('#myTable').DataTable(); // Initialize DataTable
    
    //     // Edit button handler
    //     $('.edit').on('click', function () {
    //         const memberId = $(this).data('id');
    //         const firstName = $(this).data('firstname');
    //         const lastName = $(this).data('lastname');
    //         const contactNumber = $(this).data('contact');
    //         const email = $(this).data('email');
    //         const membershipType = $(this).data('membership');
    //         const classId = $(this).data('class');
    //         const attendanceDate = $(this).data('attendance');
    //         const paymentDate = $(this).data('payment');
    //         const amount = $(this).data('amount');
    
    //         console.log('Editing Member:', { memberId, firstName, amount });
    
    //         // Populate modal fields
    //         $('#memberIdEdit').val(memberId);
    //         $('#firstNameEdit').val(firstName);
    //         $('#lastNameEdit').val(lastName);
    //         $('#contactNumberEdit').val(contactNumber);
    //         $('#emailEdit').val(email);
    //         $('#membershipTypeEdit').val(membershipType);
    //         $('#classIdEdit').val(classId);
    //         $('#attendanceDateEdit').val(attendanceDate);
    //         $('#paymentDateEdit').val(paymentDate);
    //         $('#amountEdit').val(amount);
    //     });
    // });
    
    