import 'package:flutter/material.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:html/dom.dart' as dom;
import 'package:intl/intl.dart';
import '../models/post.dart';
import '../services/post_service.dart';

class HomeScreenPostsTab extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return FutureBuilder<List<Post>>(
      future: PostsService.getInstance().posts(),
      builder: (context, snapshot) {
        if (snapshot.hasError) {
          print(snapshot.error);
          return const Center(
            child: Text('An error has occurred!'),
          );
        } else if (snapshot.hasData) {
          return PostsList(posts: snapshot.data!);
        } else {
          return const Center(
            child: CircularProgressIndicator(),
          );
        }
      }
    );
  }
}

class PostsList extends StatelessWidget {
  const PostsList({Key? key, required this.posts}) : super(key: key);

  final List<Post> posts;

  @override
  Widget build(BuildContext context) {
    return ListView.builder(
      padding: EdgeInsets.all(16),
      itemCount: posts.length,
      itemBuilder: (context, index) {
        Post post = posts[index];
        return Card(
          child: Padding(
            padding: EdgeInsets.all(16),
            child: Column(
              children: [
                Container(
                  width: double.infinity,
                  margin: EdgeInsets.only(bottom: 8),
                  child: Text(post.title, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w500)),
                ),

                Text('Written by ${post.user.name} on ${DateFormat('yyyy-MM-dd kk:mm').format(post.created_at)}', style: TextStyle(color: Colors.grey)),

                Html(
                  data: post.body,
                  style: { "body": Style(margin: EdgeInsets.zero, padding: EdgeInsets.zero) },
                  onLinkTap: (String? url, RenderContext context, Map<String, String> attributes, dom.Element? element) async {
                    if (url != null) {
                      if (await canLaunch(url as String)) await launch(url as String);
                    }
                  }
                )
              ]
            )
          )
        );
      }
    );
  }
}
