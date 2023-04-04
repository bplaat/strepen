enum NotificationType { newDeposit, newPost, lowBalance }

NotificationType? notificationTypeFromString(String type) {
  if (type == 'new_deposit') return NotificationType.newDeposit;
  if (type == 'new_post') return NotificationType.newPost;
  if (type == 'low_balance') return NotificationType.lowBalance;
  return null;
}

class NotificationData {
  final String id;
  final NotificationType type;
  final Map<String, dynamic> data;
  final DateTime? readAt;
  final DateTime createdAt;

  const NotificationData(
      {required this.id,
      required this.type,
      required this.data,
      required this.readAt,
      required this.createdAt});

  factory NotificationData.fromJson(Map<String, dynamic> json) {
    return NotificationData(
        id: json['id'],
        type: notificationTypeFromString(json['type'])!,
        data: json['data'],
        readAt:
            json['read_at'] != null ? DateTime.parse(json['read_at']) : null,
        createdAt: DateTime.parse(json['created_at']));
  }
}
