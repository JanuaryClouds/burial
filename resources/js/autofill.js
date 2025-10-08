import { faker } from "@faker-js/faker";
document.addEventListener('DOMContentLoaded', () => {
    const autofillBtn = document.getElementById('autofillBtn');
    const form = document.getElementById('kt_create_account_form');

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

    if (!autofillBtn || !form) {
        return;
    }
    autofillBtn.addEventListener('click', function (event) {
        event.preventDefault();
        console.log('autofill');
        form.querySelector('input[name="deceased[first_name]"]').value = faker.person.firstName();
        form.querySelector('input[name="deceased[middle_name]"]').value = optionalField(faker.person.middleName(), 0.8);
        form.querySelector('input[name="deceased[last_name]"]').value = faker.person.lastName();
        form.querySelector('input[name="deceased[suffix]"]').value = faker.person.suffix();
        form.querySelector('input[name="deceased[date_of_birth]"]').value = faker.date.birthdate().toISOString().split('T')[0];
        form.querySelector('input[name="deceased[date_of_death]"]').value = faker.date.recent().toISOString().split('T')[0];
        form.querySelector('input[name="deceased[address]"]').value = faker.location.streetAddress();
        autofillSelect(form, 'deceased[gender]');
        autofillSelect(form, 'deceased[barangay_id]');
        autofillSelect(form, 'deceased[religion_id]');

        form.querySelector('input[name="claimant[first_name]"]').value = faker.person.firstName();
        form.querySelector('input[name="claimant[middle_name]"]').value = optionalField(faker.person.middleName(), 0.8);
        form.querySelector('input[name="claimant[last_name]"]').value = faker.person.lastName();
        form.querySelector('input[name="claimant[suffix]"]').value = faker.person.suffix();
        form.querySelector('input[name="claimant[mobile_number]"]').value = '09' + faker.number.int({ min: 100000000, max: 999999999 });
        form.querySelector('input[name="claimant[address]"]').value = faker.location.streetAddress();
        autofillSelect(form, 'claimant[relationship_to_deceased]');
        autofillSelect(form, 'claimant[barangay_id]');

        form.querySelector('input[name="funeraria"]').value = faker.company.name();
        form.querySelector('input[name="amount"]').value = faker.finance.amount({ min: 100, max: 10000, dec: 0 });
        form.querySelector('textarea[name="remarks"]').value = optionalField(faker.lorem.sentence());
    })
})