import { faker } from "@faker-js/faker";
document.addEventListener('DOMContentLoaded', () => {
    const autofillBtn = document.getElementById('autofillBtn');
    const burialForm = document.getElementById('kt_create_account_form');
    const gisForm = document.getElementById('gisForm');
    autofillBtn ? console.log('autofillBtn found') : console.log('autofillBtn not found');
    gisForm || burialForm ? console.log('form found') : console.log('form not found');

    function autofillSelect(form, fieldName) {
        const select = form.querySelector(`select[name="${fieldName}"]`);
        if (select) {
            const options = Array.from(select.options).slice(1);
            const random = Math.floor(Math.random() * options.length);
            select.value = options[random].value;
        }
    }

    function optionalField(value, probability = 0.5) {
        return Math.random() < probability ? value : '';
    }

    // if (!autofillBtn || !burialForm || !gisForm) {
    //     return;
    // }

    autofillBtn.addEventListener('click', function (event) {
        event.preventDefault();
        console.log('autofill');

        if (burialForm) {
            burialForm.querySelector('input[name="deceased[first_name]"]').value = faker.person.firstName();
            burialForm.querySelector('input[name="deceased[middle_name]"]').value = optionalField(faker.person.middleName(), 0.8);
            burialForm.querySelector('input[name="deceased[last_name]"]').value = faker.person.lastName();
            burialForm.querySelector('input[name="deceased[suffix]"]').value = faker.person.suffix();
            burialForm.querySelector('input[name="deceased[date_of_birth]"]').value = faker.date.birthdate().toISOString().split('T')[0];
            burialForm.querySelector('input[name="deceased[date_of_death]"]').value = faker.date.recent().toISOString().split('T')[0];
            burialForm.querySelector('input[name="deceased[address]"]').value = faker.location.streetAddress();
            autofillSelect(burialForm, 'deceased[gender]');
            autofillSelect(burialForm, 'deceased[barangay_id]');
            autofillSelect(burialForm, 'deceased[religion_id]');
    
            burialForm.querySelector('input[name="claimant[first_name]"]').value = faker.person.firstName();
            burialForm.querySelector('input[name="claimant[middle_name]"]').value = optionalField(faker.person.middleName(), 0.8);
            burialForm.querySelector('input[name="claimant[last_name]"]').value = faker.person.lastName();
            burialForm.querySelector('input[name="claimant[suffix]"]').value = faker.person.suffix();
            burialForm.querySelector('input[name="claimant[mobile_number]"]').value = '09' + faker.number.int({ min: 100000000, max: 999999999 });
            burialForm.querySelector('input[name="claimant[address]"]').value = faker.location.streetAddress();
            autofillSelect(burialForm, 'claimant[relationship_to_deceased]');
            autofillSelect(burialForm, 'claimant[barangay_id]');
    
            burialForm.querySelector('input[name="funeraria"]').value = faker.company.name();
            burialForm.querySelector('input[name="amount"]').value = faker.finance.amount({ min: 100, max: 10000, dec: 0 });
            burialForm.querySelector('textarea[name="remarks"]').value = optionalField(faker.lorem.sentence());
        }

        if (gisForm) {
            gisForm.querySelector('input[name="client[name]"]').value = faker.person.fullName();
            gisForm.querySelector('input[name="client[age]"]').value = faker.number.int({ min: 1, max: 100 });
            autofillSelect(gisForm, 'client[gender]');
            gisForm.querySelector('input[name="client[date_of_birth]"]').value = faker.date.birthdate().toISOString().split('T')[0];
            gisForm.querySelector('input[name="client[address]"]').value = faker.location.streetAddress();
            autofillSelect(gisForm, 'client[barangay_id]');
            autofillSelect(gisForm, 'client[relationship_to_beneficiary]');
            autofillSelect(gisForm, 'client[civil_status]');
            autofillSelect(gisForm, 'client[religion]');
            autofillSelect(gisForm, 'client[nationality]');
            autofillSelect(gisForm, 'client[education]');
            gisForm.querySelector('input[name="client[skills]"]').value = faker.word.verb();
            gisForm.querySelector('input[name="client[income]"]').value = faker.finance.amount({ min: 100, max: 10000, dec: 0 });
            gisForm.querySelector('input[name="client[philhealth]"]').value = faker.number.int({ min: 1000000000, max: 9999999999 });
            gisForm.querySelector('input[name="client[contact_number]"]').value = '09' + faker.number.int({ min: 100000000, max: 999999999 });
            gisForm.querySelector('input[name="beneficiary[name]"]').value = faker.person.fullName();
            autofillSelect(gisForm, 'beneficiary[gender]');
            gisForm.querySelector('input[name="beneficiary[date_of_birth]"]').value = faker.date.birthdate().toISOString().split('T')[0];
            gisForm.querySelector('input[name="beneficiary[place_of_birth]"]').value = faker.location.streetAddress();
            autofillSelect(gisForm, 'beneficiary[barangay_id]');
            gisForm.querySelector('textarea[name="problem"]').value = faker.lorem.sentence();
            gisForm.querySelector('textarea[name="assessment"]').value = faker.lorem.sentence();
        }
    })
})