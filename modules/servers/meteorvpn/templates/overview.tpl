{if $error}
    <div class="errorbox">
        <strong>{$LANG.error_label}</strong> {$error}
    </div>
{/if}

<h3>{$LANG.title}</h3>

<table class="table table-condensed">
    <tr>
        <th width="120">{$LANG.active}</th>
        <td>{if isset($is_active)}{$is_active|replace:"1":$LANG.yes|replace:"":$LANG.no}{else}?{/if}</td>
    </tr>
    <tr>
        <th>{$LANG.enrolled}</th>
        <td>{if isset($enrolled)}{$enrolled|replace:"1":$LANG.yes|replace:"":$LANG.no}{else}?{/if}</td>
    </tr>
</table>

{if isset($enrollment_token)}
    <div class="alert alert-success">
        <p>
            <strong>{$LANG.enrollment_url}</strong>
            <a href="{$enrollment_url}" target="_blank">{$enrollment_url}</a>
        </p>
        <p>
            <strong>{$LANG.enrollment_token}</strong>
            <code>{$enrollment_token}</code>
        </p>
        <p>{$LANG.token_valid}</p>
    </div>
{else}
    <form method="post" onsubmit="return confirm('{$LANG.confirm_start|escape:'javascript'}');">
        <input type="hidden" name="token" value="{$token}" />
        <input type="hidden" name="meteorvpn_enroll" value="1" />
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-plug"></i> {$LANG.start_enrollment}
        </button>
    </form>
{/if}

<hr />

<h4>{$LANG.guide_title}</h4>
<ol>
    <li>{$LANG.guide_step1}</li>
    <li>{$LANG.guide_step2}</li>
    <li>{$LANG.guide_step3}</li>
    <li>
        {$LANG.guide_step4_info}<br />
        &nbsp;&nbsp;• {$LANG.guide_step4_option1}<br />
        &nbsp;&nbsp;• {$LANG.guide_step4_option2}<br />
    </li>
    <li>{$LANG.guide_step5}</li>
</ol>
