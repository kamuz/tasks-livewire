<?php

use App\Models\Contact;
use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <h1>Contacts</h1>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
        </thead>

        <tbody>
            @foreach (Contact::all() as $contact)
                <tr>
                    <td>{{ $contact->name }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>{{ $contact->phone }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>