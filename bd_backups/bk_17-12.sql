PGDMP                      |            pgrado    17.0    17.0 7    *           0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                           false            +           0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                           false            ,           0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                           false            -           1262    16499    pgrado    DATABASE     {   CREATE DATABASE pgrado WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'Spanish_Ecuador.1252';
    DROP DATABASE pgrado;
                     postgres    false            �            1255    16529    notify_messenger_messages()    FUNCTION     �   CREATE FUNCTION public.notify_messenger_messages() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            BEGIN
                PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$;
 2   DROP FUNCTION public.notify_messenger_messages();
       public               postgres    false            �            1259    16539    book    TABLE     �   CREATE TABLE public.book (
    id integer NOT NULL,
    nombre character varying(255) NOT NULL,
    autor character varying(255) NOT NULL,
    genero character varying(255) NOT NULL
);
    DROP TABLE public.book;
       public         heap r       postgres    false            �            1259    16538    book_id_seq    SEQUENCE     �   CREATE SEQUENCE public.book_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 "   DROP SEQUENCE public.book_id_seq;
       public               postgres    false    225            .           0    0    book_id_seq    SEQUENCE OWNED BY     ;   ALTER SEQUENCE public.book_id_seq OWNED BY public.book.id;
          public               postgres    false    224            �            1259    16500    doctrine_migration_versions    TABLE     �   CREATE TABLE public.doctrine_migration_versions (
    version character varying(191) NOT NULL,
    executed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    execution_time integer
);
 /   DROP TABLE public.doctrine_migration_versions;
       public         heap r       postgres    false            �            1259    16517    messenger_messages    TABLE     s  CREATE TABLE public.messenger_messages (
    id bigint NOT NULL,
    body text NOT NULL,
    headers text NOT NULL,
    queue_name character varying(190) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    available_at timestamp(0) without time zone NOT NULL,
    delivered_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);
 &   DROP TABLE public.messenger_messages;
       public         heap r       postgres    false            /           0    0 $   COLUMN messenger_messages.created_at    COMMENT     Z   COMMENT ON COLUMN public.messenger_messages.created_at IS '(DC2Type:datetime_immutable)';
          public               postgres    false    221            0           0    0 &   COLUMN messenger_messages.available_at    COMMENT     \   COMMENT ON COLUMN public.messenger_messages.available_at IS '(DC2Type:datetime_immutable)';
          public               postgres    false    221            1           0    0 &   COLUMN messenger_messages.delivered_at    COMMENT     \   COMMENT ON COLUMN public.messenger_messages.delivered_at IS '(DC2Type:datetime_immutable)';
          public               postgres    false    221            �            1259    16516    messenger_messages_id_seq    SEQUENCE     �   CREATE SEQUENCE public.messenger_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 0   DROP SEQUENCE public.messenger_messages_id_seq;
       public               postgres    false    221            2           0    0    messenger_messages_id_seq    SEQUENCE OWNED BY     W   ALTER SEQUENCE public.messenger_messages_id_seq OWNED BY public.messenger_messages.id;
          public               postgres    false    220            �            1259    16822    prestamo    TABLE     �   CREATE TABLE public.prestamo (
    id integer NOT NULL,
    student_id integer NOT NULL,
    book_id integer NOT NULL,
    fecha_prestamo integer NOT NULL,
    fecha_devolucion integer
);
    DROP TABLE public.prestamo;
       public         heap r       postgres    false            �            1259    16821    prestamo_id_seq    SEQUENCE     �   CREATE SEQUENCE public.prestamo_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 &   DROP SEQUENCE public.prestamo_id_seq;
       public               postgres    false    227            3           0    0    prestamo_id_seq    SEQUENCE OWNED BY     C   ALTER SEQUENCE public.prestamo_id_seq OWNED BY public.prestamo.id;
          public               postgres    false    226            �            1259    16532    student    TABLE     �   CREATE TABLE public.student (
    id integer NOT NULL,
    cedula integer NOT NULL,
    nombre character varying(100) NOT NULL
);
    DROP TABLE public.student;
       public         heap r       postgres    false            �            1259    16531    student_id_seq    SEQUENCE     �   CREATE SEQUENCE public.student_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 %   DROP SEQUENCE public.student_id_seq;
       public               postgres    false    223            4           0    0    student_id_seq    SEQUENCE OWNED BY     A   ALTER SEQUENCE public.student_id_seq OWNED BY public.student.id;
          public               postgres    false    222            �            1259    16507    user    TABLE     �   CREATE TABLE public."user" (
    id integer NOT NULL,
    username character varying(180) NOT NULL,
    roles json NOT NULL,
    password character varying(255) NOT NULL
);
    DROP TABLE public."user";
       public         heap r       postgres    false            �            1259    16506    user_id_seq    SEQUENCE     �   CREATE SEQUENCE public.user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 "   DROP SEQUENCE public.user_id_seq;
       public               postgres    false    219            5           0    0    user_id_seq    SEQUENCE OWNED BY     =   ALTER SEQUENCE public.user_id_seq OWNED BY public."user".id;
          public               postgres    false    218            u           2604    16542    book id    DEFAULT     b   ALTER TABLE ONLY public.book ALTER COLUMN id SET DEFAULT nextval('public.book_id_seq'::regclass);
 6   ALTER TABLE public.book ALTER COLUMN id DROP DEFAULT;
       public               postgres    false    225    224    225            r           2604    16520    messenger_messages id    DEFAULT     ~   ALTER TABLE ONLY public.messenger_messages ALTER COLUMN id SET DEFAULT nextval('public.messenger_messages_id_seq'::regclass);
 D   ALTER TABLE public.messenger_messages ALTER COLUMN id DROP DEFAULT;
       public               postgres    false    221    220    221            v           2604    16825    prestamo id    DEFAULT     j   ALTER TABLE ONLY public.prestamo ALTER COLUMN id SET DEFAULT nextval('public.prestamo_id_seq'::regclass);
 :   ALTER TABLE public.prestamo ALTER COLUMN id DROP DEFAULT;
       public               postgres    false    226    227    227            t           2604    16535 
   student id    DEFAULT     h   ALTER TABLE ONLY public.student ALTER COLUMN id SET DEFAULT nextval('public.student_id_seq'::regclass);
 9   ALTER TABLE public.student ALTER COLUMN id DROP DEFAULT;
       public               postgres    false    222    223    223            q           2604    16510    user id    DEFAULT     d   ALTER TABLE ONLY public."user" ALTER COLUMN id SET DEFAULT nextval('public.user_id_seq'::regclass);
 8   ALTER TABLE public."user" ALTER COLUMN id DROP DEFAULT;
       public               postgres    false    218    219    219            %          0    16539    book 
   TABLE DATA           9   COPY public.book (id, nombre, autor, genero) FROM stdin;
    public               postgres    false    225   �>                 0    16500    doctrine_migration_versions 
   TABLE DATA           [   COPY public.doctrine_migration_versions (version, executed_at, execution_time) FROM stdin;
    public               postgres    false    217   ?       !          0    16517    messenger_messages 
   TABLE DATA           s   COPY public.messenger_messages (id, body, headers, queue_name, created_at, available_at, delivered_at) FROM stdin;
    public               postgres    false    221   �?       '          0    16822    prestamo 
   TABLE DATA           ]   COPY public.prestamo (id, student_id, book_id, fecha_prestamo, fecha_devolucion) FROM stdin;
    public               postgres    false    227   �?       #          0    16532    student 
   TABLE DATA           5   COPY public.student (id, cedula, nombre) FROM stdin;
    public               postgres    false    223   �?                 0    16507    user 
   TABLE DATA           ?   COPY public."user" (id, username, roles, password) FROM stdin;
    public               postgres    false    219   <@       6           0    0    book_id_seq    SEQUENCE SET     9   SELECT pg_catalog.setval('public.book_id_seq', 3, true);
          public               postgres    false    224            7           0    0    messenger_messages_id_seq    SEQUENCE SET     H   SELECT pg_catalog.setval('public.messenger_messages_id_seq', 1, false);
          public               postgres    false    220            8           0    0    prestamo_id_seq    SEQUENCE SET     >   SELECT pg_catalog.setval('public.prestamo_id_seq', 1, false);
          public               postgres    false    226            9           0    0    student_id_seq    SEQUENCE SET     <   SELECT pg_catalog.setval('public.student_id_seq', 2, true);
          public               postgres    false    222            :           0    0    user_id_seq    SEQUENCE SET     :   SELECT pg_catalog.setval('public.user_id_seq', 1, false);
          public               postgres    false    218            �           2606    16546    book book_pkey 
   CONSTRAINT     L   ALTER TABLE ONLY public.book
    ADD CONSTRAINT book_pkey PRIMARY KEY (id);
 8   ALTER TABLE ONLY public.book DROP CONSTRAINT book_pkey;
       public                 postgres    false    225            x           2606    16505 <   doctrine_migration_versions doctrine_migration_versions_pkey 
   CONSTRAINT        ALTER TABLE ONLY public.doctrine_migration_versions
    ADD CONSTRAINT doctrine_migration_versions_pkey PRIMARY KEY (version);
 f   ALTER TABLE ONLY public.doctrine_migration_versions DROP CONSTRAINT doctrine_migration_versions_pkey;
       public                 postgres    false    217            �           2606    16525 *   messenger_messages messenger_messages_pkey 
   CONSTRAINT     h   ALTER TABLE ONLY public.messenger_messages
    ADD CONSTRAINT messenger_messages_pkey PRIMARY KEY (id);
 T   ALTER TABLE ONLY public.messenger_messages DROP CONSTRAINT messenger_messages_pkey;
       public                 postgres    false    221            �           2606    16827    prestamo prestamo_pkey 
   CONSTRAINT     T   ALTER TABLE ONLY public.prestamo
    ADD CONSTRAINT prestamo_pkey PRIMARY KEY (id);
 @   ALTER TABLE ONLY public.prestamo DROP CONSTRAINT prestamo_pkey;
       public                 postgres    false    227            �           2606    16537    student student_pkey 
   CONSTRAINT     R   ALTER TABLE ONLY public.student
    ADD CONSTRAINT student_pkey PRIMARY KEY (id);
 >   ALTER TABLE ONLY public.student DROP CONSTRAINT student_pkey;
       public                 postgres    false    223            {           2606    16514    user user_pkey 
   CONSTRAINT     N   ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);
 :   ALTER TABLE ONLY public."user" DROP CONSTRAINT user_pkey;
       public                 postgres    false    219            |           1259    16528    idx_75ea56e016ba31db    INDEX     [   CREATE INDEX idx_75ea56e016ba31db ON public.messenger_messages USING btree (delivered_at);
 (   DROP INDEX public.idx_75ea56e016ba31db;
       public                 postgres    false    221            }           1259    16527    idx_75ea56e0e3bd61ce    INDEX     [   CREATE INDEX idx_75ea56e0e3bd61ce ON public.messenger_messages USING btree (available_at);
 (   DROP INDEX public.idx_75ea56e0e3bd61ce;
       public                 postgres    false    221            ~           1259    16526    idx_75ea56e0fb7336f0    INDEX     Y   CREATE INDEX idx_75ea56e0fb7336f0 ON public.messenger_messages USING btree (queue_name);
 (   DROP INDEX public.idx_75ea56e0fb7336f0;
       public                 postgres    false    221            �           1259    16829    idx_f4d874f216a2b381    INDEX     L   CREATE INDEX idx_f4d874f216a2b381 ON public.prestamo USING btree (book_id);
 (   DROP INDEX public.idx_f4d874f216a2b381;
       public                 postgres    false    227            �           1259    16828    idx_f4d874f2cb944f1a    INDEX     O   CREATE INDEX idx_f4d874f2cb944f1a ON public.prestamo USING btree (student_id);
 (   DROP INDEX public.idx_f4d874f2cb944f1a;
       public                 postgres    false    227            y           1259    16515    uniq_identifier_username    INDEX     V   CREATE UNIQUE INDEX uniq_identifier_username ON public."user" USING btree (username);
 ,   DROP INDEX public.uniq_identifier_username;
       public                 postgres    false    219            �           2620    16530 !   messenger_messages notify_trigger    TRIGGER     �   CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON public.messenger_messages FOR EACH ROW EXECUTE FUNCTION public.notify_messenger_messages();
 :   DROP TRIGGER notify_trigger ON public.messenger_messages;
       public               postgres    false    221    228            �           2606    16835    prestamo fk_f4d874f216a2b381    FK CONSTRAINT     z   ALTER TABLE ONLY public.prestamo
    ADD CONSTRAINT fk_f4d874f216a2b381 FOREIGN KEY (book_id) REFERENCES public.book(id);
 F   ALTER TABLE ONLY public.prestamo DROP CONSTRAINT fk_f4d874f216a2b381;
       public               postgres    false    225    4740    227            �           2606    16830    prestamo fk_f4d874f2cb944f1a    FK CONSTRAINT     �   ALTER TABLE ONLY public.prestamo
    ADD CONSTRAINT fk_f4d874f2cb944f1a FOREIGN KEY (student_id) REFERENCES public.student(id);
 F   ALTER TABLE ONLY public.prestamo DROP CONSTRAINT fk_f4d874f2cb944f1a;
       public               postgres    false    4738    223    227            %   m   x�3��IT8�+�$37Q!/�$��+?1/Q�7�(���3(?71/9�ˈ383�4U!7S�,��ӱ(��=?%��Ƙӱ8-�"3��1'�B�7�(���7��$�(3�+F��� 0�$�         �   x��α�0�9�
~���1<�v픥�P����;�n�<�<<ݴ��}Y���ޟ粭G)�y?��RM2�ol)-ш��o �8U�B�ԙ0p� (t� 6�)^A�B����h���53T�_��Xׇ���c� ��Z�      !      x������ � �      '      x������ � �      #   H   x�3�4475CKN�̜�̔J�����̜�D.#���������%gXfYfb^��SbNJjQQbn>W� _8l         U   x�3�LL���㌎�T1�T14V�KwvJN,�uwO7q��-2�5��������KL��͵Lv��6���v-��N�NJ����� �V     