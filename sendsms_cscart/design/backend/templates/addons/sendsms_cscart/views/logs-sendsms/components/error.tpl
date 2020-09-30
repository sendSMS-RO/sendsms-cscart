<tr class="logs-send-sms" {*id="error_{$error[0]}" *}>
    <td class="left">
        {$error[0]}
    </td>
    <td class="left">
        {$error[1]}
    </td>
    <td class="left">
        {$error[2]}
    </td>
    <td class="left">
        {$error[3]}
    </td>
    <td class="left">
        {$error[4]}
    </td>
    <td class="left">
        <div title='{$error[5]}'>    
            {$error[5]|truncate:20}
        </div>
    </td>
    <td class="left">
        {$error[6]}
    </td>
</tr>