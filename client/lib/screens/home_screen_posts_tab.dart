import 'package:flutter/material.dart';
import 'package:flutter_gen/gen_l10n/app_localizations.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:html/dom.dart' as dom;
import 'package:intl/intl.dart';
import '../models/post.dart';
import '../services/post_service.dart';

class HomeScreenPostsTab extends StatefulWidget {
  @override
  State createState() {
    return _HomeScreenPostsTabState();
  }
}

class _HomeScreenPostsTabState extends State {
  ScrollController _scrollController = ScrollController();

  List<Post> _posts = [];
  List<int> _loadedPages = [];
  int _page = 1;
  bool _isLoading = true;
  bool _hasError = false;
  bool _isDone = false;

  @override
  void initState() {
    super.initState();
    loadNextPage();
    _scrollController.addListener(() {
      if (!_isLoading && _scrollController.position.pixels > _scrollController.position.maxScrollExtent * 0.9) {
        loadNextPage();
      }
    });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  void loadNextPage() async {
    if (_isDone) return;

    _isLoading = true;
    List<Post> newPosts;
    try {
      newPosts = await PostsService.getInstance().posts(page: _page, forceReload: _loadedPages.contains(_page));
      if (!_loadedPages.contains(_page)) {
        _loadedPages.add(_page);
      }
    } catch (exception) {
      print('HomeScreenPostsTab error: ${exception}');
      _isLoading = false;
      if (mounted) {
        setState(() => _hasError = true);
      }
      return;
    }
    if (newPosts.length > 0) {
      _posts.addAll(newPosts);
      _page++;
    } else {
      _isDone = true;
    }

    _isLoading = false;
    if (newPosts.length > 0 && mounted) {
      setState(() => _posts = _posts);
    }
  }

  @override
  Widget build(BuildContext context) {
    final lang = AppLocalizations.of(context)!;
    return RefreshIndicator(
      onRefresh: () async {
        _posts = [];
        _page = 1;
        _isLoading = false;
        _isDone = false;
        loadNextPage();
      },
      child: _hasError ? Center(
        child: Text(lang.home_posts_error),
      ) : (
        _posts.length > 0 ? ListView.builder(
          controller: _scrollController,
          padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          itemCount: _posts.length,
          itemBuilder: (context, index) => PostItem(post: _posts[index])
        ) : (
          _isLoading ? Center(
            child: CircularProgressIndicator()
          ) : Center(
            child: Text(lang.home_posts_empty),
          )
        )
      )
    );
  }
}

class PostItem extends StatefulWidget {
  final Post post;

  const PostItem({Key? key, required this.post}) : super(key: key);

  @override
  State createState() {
    return _PostItemState(post: post);
  }
}

class _PostItemState extends State {
  final Post post;

  _PostItemState({required this.post});

  @override
  Widget build(BuildContext context) {
    final lang = AppLocalizations.of(context)!;
    final brightness = MediaQuery.of(context).platformBrightness;
    return Container(
      margin: EdgeInsets.symmetric(vertical: 8),
      child: Card(
        clipBehavior: Clip.antiAliasWithSaveLayer,
        child: Column(
          children: [
            if (post.image != null) ...[
              AspectRatio(
                aspectRatio: 16 / 9,
                child: Container(
                  decoration: BoxDecoration(
                    image: DecorationImage(
                      fit: BoxFit.cover,
                      image: CachedNetworkImageProvider(post.image!)
                    )
                  )
                )
              )
            ],

            Padding(
              padding: EdgeInsets.all(16),
              child: Column(
                children: [
                  Container(
                    width: double.infinity,
                    margin: EdgeInsets.only(bottom: 8),
                    child: Text(post.title, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w500)),
                  ),

                  Container(
                    width: double.infinity,
                    child: Text(lang.home_posts_written_by(post.user!.name, DateFormat('yyyy-MM-dd kk:mm').format(post.created_at)), style: TextStyle(color: Colors.grey))
                  ),

                  Html(
                    data: post.body,
                    style: { "body": Style(margin: EdgeInsets.zero, padding: EdgeInsets.zero) },
                    onLinkTap: (String? url, RenderContext context, Map<String, String> attributes, dom.Element? element) async {
                      if (url != null) {
                        if (await canLaunch(url as String)) await launch(url as String);
                      }
                    }
                  ),

                  Row(
                    children: [
                      // Like button
                      Expanded(
                        flex: 1,
                        child: post.userLiked ? ElevatedButton.icon(
                          onPressed: () async {
                            await post.like();
                            setState(() {});
                          },
                          style: ElevatedButton.styleFrom(
                            primary: Colors.green,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                            padding: EdgeInsets.symmetric(horizontal: 24, vertical: 12)
                          ),
                          icon: Icon(Icons.thumb_up_alt, color: Colors.white),
                          label: Text(post.likes > 0 ? post.likes.toString() : lang.home_posts_like, style: TextStyle(color: Colors.white))
                        ) : OutlinedButton.icon(
                          onPressed: () async {
                            await post.like();
                            setState(() {});
                          },
                          style: OutlinedButton.styleFrom(
                            primary: brightness == Brightness.light ? Colors.black : Colors.white,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                            padding: EdgeInsets.symmetric(horizontal: 24, vertical: 12)
                          ),
                          icon: Icon(Icons.thumb_up_alt_outlined),
                          label: Text(post.likes > 0 ? post.likes.toString() : lang.home_posts_like)
                        )
                      ),

                      SizedBox(width: 16),

                      // Dislike button
                      Expanded(
                        flex: 1,
                        child: post.userDisliked ? ElevatedButton.icon(
                          onPressed: () async {
                            await post.dislike();
                            setState(() {});
                          },
                          style: ElevatedButton.styleFrom(
                            primary: Colors.red,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                            padding: EdgeInsets.symmetric(horizontal: 24, vertical: 12)
                          ),
                          icon: Icon(Icons.thumb_down_alt, color: Colors.white),
                          label: Text(post.dislikes > 0 ? post.dislikes.toString() : lang.home_posts_dislike, style: TextStyle(color: Colors.white))
                        ) : OutlinedButton.icon(
                          onPressed: () async {
                            await post.dislike();
                            setState(() {});
                          },
                          style: OutlinedButton.styleFrom(
                            primary: brightness == Brightness.light ? Colors.black : Colors.white,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                            padding: EdgeInsets.symmetric(horizontal: 24, vertical: 12)
                          ),
                          icon: Icon(Icons.thumb_down_alt_outlined),
                          label: Text(post.dislikes > 0 ? post.dislikes.toString() : lang.home_posts_dislike)
                        )
                      )
                    ]
                  )
                ]
              )
            )
          ]
        )
      )
    );
  }
}
