<div id="reply-modal" class="modal-overlay hidden" onclick="closeReplyModal(event)">
    <div class="modal-card" onclick="event.stopPropagation()">
        <div class="panel" style="margin-bottom: 0;">
            <div class="panel-head">
                <div class="panel-title">
                    <i class="ti ti-mail-forward"></i> Reply to Message
                </div>
                <button type="button" class="btn btn-ghost btn-sm" onclick="closeReplyModal()">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="reply-form" method="POST" action="">
                @csrf
                <div class="form-body">
                    <div style="background: var(--bg-base); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 16px; margin-bottom: 20px;">
                        <div style="font-size: 13.5px; font-weight: 700; color: var(--text-primary);" id="modal-sender-name">Sender Name</div>
                        <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;" id="modal-sender-email">sender@example.com</div>
                        <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;" id="modal-message-date">June 16, 2026 04:00 PM</div>
                        <p style="font-size: 12.5px; line-height: 1.6; color: var(--text-secondary); margin-top: 12px; white-space: pre-wrap; word-break: break-all;" id="modal-original-msg">
                            Original message content...
                        </p>
                    </div>
                    
                    <div class="field-group">
                        <label>Email Subject</label>
                        <input type="text" name="subject" id="reply-subject" class="field" required>
                    </div>
                    <div class="field-group">
                        <label>Reply Message</label>
                        <textarea name="message" id="reply-body" class="field" rows="6" placeholder="Write your reply here..." required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary full-width" style="margin-top: 10px;">
                        <i class="ti ti-send"></i> Send Email Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
