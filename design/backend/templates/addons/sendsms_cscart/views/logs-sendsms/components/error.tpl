<tr class="logs-send-sms" {*id="error_{$error[0]}" *}>
    <td class="nowrap" data-th="ID">
        {$error[0]}
    </td>
    <td class="nowrap" data-th="Status">
        {$error[1]}
    </td>
    <td class="nowrap" data-th="Type">
        {$error[2]}
    </td>
    <td class="nowrap" data-th="Send to">
        {$error[3]}
    </td>
    <td class="nowrap" data-th="Date">
        {$error[4]}
    </td>
    <td class="nowrap" data-th="Message">
        <div title='{$error[5]}'>    
            {$error[5]|truncate:20}
        </div>
    </td>
    <td class="nowrap" data-th="Info">
        {$error[6]}
    </td>
</tr>